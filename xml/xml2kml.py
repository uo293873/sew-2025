
""""
Crea archivos KML con puntos y líneas
autor: Alejandro Aldea Viana - UO293873
"""

import xml.etree.ElementTree as ET

class Kml(object):
    """
    Genera archivo KML con el circuito de Sepang uniendo lineas y puntos
    @version 1.0 22/Octumbre/2025
    @author: Alejandro Aldea Viana - UO293873
    """
    def __init__(self):
        """
        Crea el elemento raíz y el espacio de nombres
        """
        self.raiz = ET.Element('kml', xmlns="http://www.opengis.net/kml/2.2")
        self.doc = ET.SubElement(self.raiz,'Document')

    def addPlacemark(self,nombre,descripcion,long,lat,alt, modoAltitud):
        """
        Añade un elemento <Placemark> con puntos <Point>
        """
        pm = ET.SubElement(self.doc,'Placemark')
        ET.SubElement(pm,'name').text = nombre
        ET.SubElement(pm,'description').text = descripcion
        punto = ET.SubElement(pm,'Point')
        ET.SubElement(punto,'coordinates').text = '{},{},{}'.format(long,lat,alt)
        ET.SubElement(punto,'altitudeMode').text = modoAltitud

    def addLineString(self,nombre,extrude,tesela, listaCoordenadas, modoAltitud, color, ancho):
        """
        Añade un elemento <Placemark> con líneas <LineString>
        """
        ET.SubElement(self.doc,'name').text = nombre
        pm = ET.SubElement(self.doc,'Placemark')
        ls = ET.SubElement(pm, 'LineString')
        ET.SubElement(ls,'extrude').text = extrude
        ET.SubElement(ls,'tessellation').text = tesela
        ET.SubElement(ls,'coordinates').text = listaCoordenadas
        ET.SubElement(ls,'altitudeMode').text = modoAltitud
        estilo = ET.SubElement(pm, 'Style')
        linea = ET.SubElement(estilo, 'LineStyle')
        ET.SubElement (linea, 'color').text = color
        ET.SubElement (linea, 'width').text = ancho

    def escribir(self,nombreArchivoKML):
        """
        Escribe el archivo KML con declaración y codificación
        """
        arbol = ET.ElementTree(self.raiz)
        """
        Introduce indentacióon y saltos de línea
        para generar XML en modo texto
        """
        ET.indent(arbol)
        arbol.write(nombreArchivoKML, encoding='utf-8', xml_declaration=True)

def main():
    print(Kml.__doc__)
    
    # Nombre del archivo de entrada y salida
    archivoXML = "circuitoEsquema.xml"
    nombreKML = "circuito.kml"
    
    try:
        arbol = ET.parse(archivoXML)
        raiz = arbol.getroot()
        namespace = {'c': 'http://www.uniovi.es'}
        nuevoKML = Kml()
        
        # //nombre - descendientes 'nombre' de la raíz
        nombre_circuito = raiz.find('.//c:nombre', namespace).text
        # //localidad - descendientes 'localidad' de la raíz  
        localidad = raiz.find('.//c:localidad', namespace).text
        # //pais - descendientes 'pais' de la raíz
        pais = raiz.find('.//c:pais', namespace).text
        
        # //coordenadasOrigen/longitudGeo - descendientes 'longitudGeo' hijos de 'coordenadasOrigen'
        longitud_origen = float(raiz.find('.//c:coordenadasOrigen/c:longitudGeo', namespace).text)
        latitud_origen = float(raiz.find('.//c:coordenadasOrigen/c:latitudGeo', namespace).text)
        
        # Añadir el punto de la meta
        nuevoKML.addPlacemark('Línea de Meta',
                            f'{nombre_circuito} - {localidad}, {pais}',
                            longitud_origen, latitud_origen, 0,
                            'clampToGround')
        
        # //tramo/coordenadas/longitudGeo - descendientes 'longitudGeo' de 'coordenadas' que son hijos de 'tramo'
        coordenadas_longitud = raiz.findall('.//c:tramo/c:coordenadas/c:longitudGeo', namespace)
        coordenadas_latitud = raiz.findall('.//c:tramo/c:coordenadas/c:latitudGeo', namespace)
        
        # Construir la cadena de coordenadas para la línea del circuito
        # empezando desde el punto de meta
        coordenadas_circuito = f"{longitud_origen},{latitud_origen}\n"
        
        for i in range(len(coordenadas_longitud)):
            longitud = float(coordenadas_longitud[i].text)
            latitud = float(coordenadas_latitud[i].text)
            coordenadas_circuito += f"{longitud},{latitud}\n"
        
        coordenadas_circuito += f"{longitud_origen},{latitud_origen}"
        
        # Añadir la línea del circuito
        nuevoKML.addLineString(f"Trazado {nombre_circuito}", "1", "1",
                             coordenadas_circuito, 'relativeToGround',
                             "#ff0000ff", "3")
        
        # Escribir el archivo KML
        nuevoKML.escribir(nombreKML)
        
    except FileNotFoundError:
        print(f"Error: No se encontró el archivo {archivoXML}")
    except ET.ParseError as e:
        print(f"Error al parsear el archivo XML: {e}")
    except Exception as e:
        print(f"Error inesperado: {e}")

if __name__ == "__main__":
    main() 