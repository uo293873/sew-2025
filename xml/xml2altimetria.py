# xml2altimetria.py
# -*- coding: utf-8 -*-
""""
Crea archivos SVG con el perfil altimétrico del circuito
@version 1.0 22/Octubre/2025
@author: Alejandro Aldea Viana - UO293873
"""
import xml.etree.ElementTree as ET

class Svg(object):
    """
    Genera archivos SVG con rectángulos, círculos, líneas, polilíneas y texto
    @version 1.0 18/Octubre/2024
    @author: Juan Manuel Cueva Lovelle. Universidad de Oviedo
    """
    def __init__(self, width="800", height="600"):
        """
        Crea el elemento raíz, el espacio de nombres y la versión
        """
        self.raiz = ET.Element('svg', xmlns="http://www.w3.org/2000/svg", version="2.0", 
                              width=width, height=height, viewBox=f"0 0 {width} {height}")

    def addRect(self,x,y,width,height,fill, strokeWidth,stroke):
        """
        Añade un elemento rect
        """
        ET.SubElement(self.raiz,'rect',
                     x=str(x),
                     y=str(y),
                     width=str(width),
                     height=str(height),
                     fill=fill,
                     **{'stroke-width': str(strokeWidth)},
                     stroke=stroke)

    def addCircle(self,cx,cy,r,fill):
        """
        Añade un elemento circle
        """
        ET.SubElement(self.raiz,'circle',
                     cx=str(cx),
                     cy=str(cy),
                     r=str(r),
                     fill=fill)

    def addLine(self,x1,y1,x2,y2,stroke,strokeWidth):
        """
        Añade un elemento line
        """
        ET.SubElement(self.raiz,'line',
                     x1=str(x1),
                     y1=str(y1),
                     x2=str(x2),
                     y2=str(y2),
                     stroke=stroke,
                     **{'stroke-width': str(strokeWidth)})

    def addPolyline(self,points,stroke,strokeWidth,fill):
        """
        Añade un elemento polyline
        """
        ET.SubElement(self.raiz,'polyline',
                     points=points,
                     stroke=stroke,
                     **{'stroke-width': str(strokeWidth)},
                     fill=fill)

    def addText(self,texto,x,y,fontFamily,fontSize,style):
        """
        Añade un elemento texto
        """
        elemento_texto = ET.SubElement(self.raiz,'text',
                                     x=str(x),
                                     y=str(y),
                                     **{'font-family': fontFamily},
                                     **{'font-size': str(fontSize)})
        if style:
            elemento_texto.set('style', style)
        elemento_texto.text = texto

    def escribir(self,nombreArchivoSVG):
        """
        Escribe el archivo SVG con declaración y codificación
        """
        arbol = ET.ElementTree(self.raiz)
        """
        Introduce indentación y saltos de línea
        para generar XML en modo texto
        """
        ET.indent(arbol)
        arbol.write(nombreArchivoSVG,
                   encoding='utf-8',
                   xml_declaration=True)

    def ver(self):
        """
        Muestra el archivo SVG. Se utiliza para depurar
        """
        print("\nElemento raiz = ", self.raiz.tag)
        if self.raiz.text != None:
            print("Contenido = " , self.raiz.text.strip('\n')) #strip() elimina los '\n' del string
        else:
            print("Contenido = " , self.raiz.text)
        print("Atributos = " , self.raiz.attrib)
        # Recorrido de los elementos del árbol
        for hijo in self.raiz.findall('.//'): # Expresión XPath
            print("\nElemento = " , hijo.tag)
            if hijo.text != None:
                print("Contenido = ", hijo.text.strip('\n')) #strip() elimina los '\n' del string
            else:
                print("Contenido = ", hijo.text)
            print("Atributos = ", hijo.attrib)

def main():
    print(Svg.__doc__)
    
    # Nombre del archivo de entrada y salida
    archivoXML = "circuitoEsquema.xml"
    nombreSVG = "altimetria.svg"
    
    try:
        # Leer y parsear el archivo XML
        arbol = ET.parse(archivoXML)
        raiz = arbol.getroot()
        
        # Definir el namespace
        namespace = {'c': 'http://www.uniovi.es'}
        
        # Obtener información del circuito usando XPath
        nombre_circuito = raiz.find('.//c:nombre', namespace).text
        localidad = raiz.find('.//c:localidad', namespace).text
        
        # Obtener altitud y distancia de origen usando XPath
        altitud_origen = float(raiz.find('.//c:coordenadasOrigen/c:altitudGeo', namespace).text)
        
        # Obtener todas las distancias y altitudes de los tramos usando XPath
        distancias = raiz.findall('.//c:tramo/c:distancia', namespace)
        altitudes = raiz.findall('.//c:tramo/c:coordenadas/c:altitudGeo', namespace)
        
        # Procesar datos para el perfil altimétrico
        distancia_acumulada = [0]  # Empezar en 0
        lista_altitudes = [altitud_origen]  # Empezar con altitud de origen
        
        distancia_total = 0
        for i in range(len(distancias)):
            distancia_total += float(distancias[i].text)
            distancia_acumulada.append(distancia_total)
            lista_altitudes.append(float(altitudes[i].text))
        
        # Configuración del SVG
        ancho_svg = 1000
        alto_svg = 600
        margen = 80
        
        # Área de dibujo
        ancho_grafico = ancho_svg - 2 * margen
        alto_grafico = alto_svg - 2 * margen
        
        # Escalas
        max_distancia = max(distancia_acumulada)
        max_altitud = max(lista_altitudes)
        min_altitud = min(lista_altitudes)
        rango_altitud = max_altitud - min_altitud
        
        # Crear objeto SVG
        nuevoSVG = Svg(str(ancho_svg), str(alto_svg))
        
        nuevoSVG.addRect(0, 0, ancho_svg, alto_svg, "#0A1A2F", "0", "none")
        
        # Añadir título
        nuevoSVG.addText(f"Perfil Altimétrico - {nombre_circuito}", 
                        ancho_svg//2, 30, "Trebuchet MS", "18", "text-anchor: middle; font-weight: bold; fill: #FF6600")
        nuevoSVG.addText(f"{localidad}", 
                        ancho_svg//2, 50, "Trebuchet MS", "14", "text-anchor: middle; fill: #E0E0E0")
        
        # Construir puntos para la polilínea del perfil
        puntos_perfil = []
        for i in range(len(distancia_acumulada)):
            x = margen + (distancia_acumulada[i] / max_distancia) * ancho_grafico
            y = alto_svg - margen - ((lista_altitudes[i] - min_altitud) / rango_altitud) * alto_grafico
            puntos_perfil.append(f"{x:.1f},{y:.1f}")
        
        # Cerrar la polilínea para crear efecto suelo
        puntos_perfil.append(f"{ancho_svg - margen},{alto_svg - margen}")  # Esquina inferior derecha
        puntos_perfil.append(f"{margen},{alto_svg - margen}")  # Esquina inferior izquierda
        
        puntos_str = " ".join(puntos_perfil)
        
        # Añadir la polilínea del perfil altimétrico
        nuevoSVG.addPolyline(puntos_str, "#FF6600", "3", "rgba(255, 102, 0, 0.15)")
        
        # Añadir ejes
        # Eje horizontal (distancias)
        nuevoSVG.addLine(margen, alto_svg - margen, ancho_svg - margen, alto_svg - margen, "#E0E0E0", "2")
        
        # Eje vertical (altitudes)
        nuevoSVG.addLine(margen, margen, margen, alto_svg - margen, "#E0E0E0", "2")
        
        # Etiquetas del eje horizontal (distancias)
        num_marcas_dist = 5
        for i in range(num_marcas_dist + 1):
            distancia = (max_distancia / num_marcas_dist) * i
            x = margen + (i / num_marcas_dist) * ancho_grafico
            y = alto_svg - margen + 15
            nuevoSVG.addText(f"{distancia:.0f}m", x, y, "Trebuchet MS", "12", "text-anchor: middle; fill: #CCCCCC")
            # Línea de marca
            nuevoSVG.addLine(x, alto_svg - margen, x, alto_svg - margen + 5, "#E0E0E0", "1")
        
        # Etiquetas del eje vertical (altitudes)
        num_marcas_alt = 5
        for i in range(num_marcas_alt + 1):
            altitud = min_altitud + (rango_altitud / num_marcas_alt) * i
            x = margen - 10
            y = alto_svg - margen - (i / num_marcas_alt) * alto_grafico + 5
            nuevoSVG.addText(f"{altitud:.1f}m", x, y, "Trebuchet MS", "12", "text-anchor: end; fill: #CCCCCC")
            # Línea de marca
            nuevoSVG.addLine(margen - 5, y - 5, margen, y - 5, "#E0E0E0", "1")
        
        # Etiquetas de los ejes
        nuevoSVG.addText("Distancia (m)", ancho_svg//2, alto_svg - 20, "Trebuchet MS", "14", "text-anchor: middle; font-weight: bold; fill: #E0E0E0")
        nuevoSVG.addText("Altitud (m)", 20, alto_svg//2, "Trebuchet MS", "14", "text-anchor: middle; font-weight: bold; writing-mode: tb; glyph-orientation-vertical: 0; fill: #E0E0E0")
        
        # Escribir el archivo SVG
        nuevoSVG.escribir(nombreSVG)

        
    except FileNotFoundError:
        print(f"Error: No se encontró el archivo {archivoXML}")
    except ET.ParseError as e:
        print(f"Error al parsear el archivo XML: {e}")
    except Exception as e:
        print(f"Error inesperado: {e}")

if __name__ == "__main__":
    main() 