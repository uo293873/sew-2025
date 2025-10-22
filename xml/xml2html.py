# xml2html.py
# -*- coding: utf-8 -*-
""""
Crea archivos HTML con información del circuito
@version 1.0 22/Octubre/2025
@author: Alejandro Aldea Viana - UO293873
"""
import xml.etree.ElementTree as ET

class Html(object):
    """
    Genera archivos HTML con información del circuito
    @version 1.0 22/Octubre/2025
    @author: Alejandro Aldea Viana - UO293873
    """
    def __init__(self, titulo="Información del Circuito"):
        """
        Crea la estructura básica del documento HTML
        """
        self.titulo = titulo
        self.contenido = []

    def addDocType(self):
        """
        Añade el DOCTYPE HTML5
        """
        return "<!DOCTYPE html>"

    def addHtmlOpen(self, lang="es"):
        """
        Añade la etiqueta de apertura HTML con idioma
        """
        return f'<html lang="{lang}">'

    def addHtmlClose(self):
        """
        Añade la etiqueta de cierre HTML
        """
        return "</html>"

    def addHead(self, titulo, css_path="estilo/estilo.css"):
        """
        Genera la sección head completa manteniendo la estructura original
        """
        head = f'''<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <meta name ="author" content ="Alejandro Aldea Viana - UO293873" />
    <meta name ="description" content ="Información del circuito del proyecto MotoGP-Desktop" />
    <meta name ="keywords" content ="MotoGP, motociclismo, deportes, velocidad, circuito" />
    <meta name ="viewport" content ="width=device-width, initial-scale=1.0" />
    <title>{titulo}</title>
    <link rel="icon" type="image/x-icon" href="../multimedia/icono.png" />
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
</head>'''
        return head

    def addBodyOpen(self):
        """
        Añade la etiqueta de apertura del body
        """
        return "<body>"

    def addBodyClose(self):
        """
        Añade la etiqueta de cierre del body
        """
        return "</body>"

    def addHeader(self, titulo):
        """
        Añade el header con navegación como en circuito.html original
        """
        header = f'''<header>
        <h1><a href="../index.html" title="Ir a la página principal">MotoGP Desktop</a></h1>
        <nav>
            <a href="../index.html" title="Página de inicio">Inicio</a>
            <a href="../piloto.html" title="Información del piloto">Piloto</a>
            <a href="../circuito.html" title="Información del circuito" class="active">Circuito</a>
            <a href="../meteorologia.html" title="Información sobre meteorología">Meteorología</a>
            <a href="../clasificaciones.html" title="Clasificaciones de MotoGP">Clasificaciones</a>
            <a href="../juegos.html" title="Juegos de MotoGP">Juegos</a>
            <a href="../ayuda.html" title="Ayuda de MotoGP">Ayuda</a>
        </nav>
    </header>'''
        return header

    def addNav(self):
        """
        Añade las migas de pan como en circuito.html original
        """
        nav = '''<p>Estás en: <a href="../index.html">Inicio</a> &gt; Circuito</p>'''
        return nav

    def addMainOpen(self):
        """
        Añade la etiqueta de apertura del main
        """
        return "<main>"

    def addMainClose(self):
        """
        Añade la etiqueta de cierre del main
        """
        return "</main>"

    def addSection(self, titulo, contenido):
        """
        Añade una sección con título y contenido
        """
        section = f'''<section>
    <h2>{titulo}</h2>
    {contenido}
</section>'''
        return section

    def addParagraph(self, texto):
        """
        Añade un párrafo
        """
        return f"    <p>{texto}</p>"

    def addList(self, items, ordered=False):
        """
        Añade una lista (ordenada o no ordenada)
        """
        tag = "ol" if ordered else "ul"
        list_items = "".join([f"        <li>{item}</li>\n" for item in items])
        return f"    <{tag}>\n{list_items}    </{tag}>"

    def addDefinitionList(self, definitions):
        """
        Añade una lista de definiciones
        """
        dl_content = ""
        for term, description in definitions:
            dl_content += f"        <dt>{term}</dt>\n        <dd>{description}</dd>\n"
        return f"    <dl>\n{dl_content}    </dl>"

    def addImage(self, src, alt, caption=None):
        """
        Añade una imagen con caption opcional y mejor formato
        """
        if caption:
            return f'''    <figure>
        <img src="../{src}" alt="{alt}" style="max-width: 100%; height: auto;">
        <figcaption>{caption}</figcaption>
    </figure>'''
        else:
            return f'    <img src="../{src}" alt="{alt}" style="max-width: 100%; height: auto;">'
    
    def addVideo(self, src, caption=None):
        """
        Añade un video con caption opcional
        """
        if caption:
            return f'''    <figure>
        <video controls>
            <source src="../{src}" type="video/mp4">
            Tu navegador no soporta el elemento video.
        </video>
        <figcaption>{caption}</figcaption>
    </figure>'''
        else:
            return f'''    <video controls>
        <source src="../{src}" type="video/mp4">
        Tu navegador no soporta el elemento video.
    </video>'''

    def addLink(self, url, text=None):
        """
        Añade un enlace clicable
        """
        link_text = text if text else url
        return f'<a href="{url}">{link_text}</a>'


    def escribir(self, nombreArchivoHTML, contenido_completo):
        """
        Escribe el archivo HTML completo
        """
        with open(nombreArchivoHTML, 'w', encoding='utf-8') as archivo:
            archivo.write(contenido_completo)

def main():
    # Archivos
    archivoXML = "circuitoEsquema.xml"
    nombreHTML = "InfoCircuito.html"
    
    try:
        # Leer y parsear el archivo XML
        arbol = ET.parse(archivoXML)
        raiz = arbol.getroot()
        
        # Definir el namespace
        namespace = {'c': 'http://www.uniovi.es'}
        
        # Extraer información usando expresiones XPath
        # Información básica del circuito
        nombre_circuito = raiz.find('.//c:nombre', namespace).text
        longitud = raiz.find('.//c:longitud', namespace)
        anchura = raiz.find('.//c:anchura', namespace)
        fecha = raiz.find('.//c:fecha', namespace).text
        hora_inicio = raiz.find('.//c:horaInicio', namespace).text
        numero_vueltas = raiz.find('.//c:numeroVueltas', namespace).text
        localidad = raiz.find('.//c:localidad', namespace).text
        pais = raiz.find('.//c:pais', namespace).text
        patrocinador = raiz.find('.//c:patrocinadorPrincipal', namespace).text
        
        # Referencias
        referencias = raiz.findall('.//c:referencias/c:referencia', namespace)
        
        # Multimedia
        fotos = raiz.findall('.//c:fotos/c:foto', namespace)
        videos = raiz.findall('.//c:videos/c:video', namespace)
        
        # Coordenadas de origen
        coord_origen = raiz.find('.//c:coordenadasOrigen', namespace)
        longitud_geo = coord_origen.find('c:longitudGeo', namespace).text
        latitud_geo = coord_origen.find('c:latitudGeo', namespace).text
        altitud_geo = coord_origen.find('c:altitudGeo', namespace).text
        
        # Información del vencedor
        vencedor = raiz.find('.//c:vencedor', namespace)
        nombre_vencedor = vencedor.find('c:nombrePiloto', namespace).text
        tiempo_carrera = vencedor.find('c:tiempoCarrera', namespace).text
        
        # Clasificación mundial
        clasificados = raiz.findall('.//c:clasificacionMundial/c:pilotoClasificado', namespace)
        
        # Crear objeto HTML
        html = Html()
        
        # Construir el documento HTML
        documento = []
        
        # Estructura básica
        documento.append(html.addDocType())
        documento.append(html.addHtmlOpen())
        documento.append(html.addHead(f"MotoGP-Circuito"))
        documento.append(html.addBodyOpen())
        
        # Header con navegación original
        documento.append(html.addHeader("MotoGP Desktop"))
        
        # Migas de pan
        documento.append(html.addNav())
        
        # Main content
        documento.append(html.addMainOpen())
        
        # Título principal del circuito
        documento.append(f'<section>')
        documento.append(f'    <h2>Información del circuito - {nombre_circuito}</h2>')
        documento.append(f'    <p>Circuito de {localidad}, {pais}</p>')
        documento.append(f'</section>')
        
        # Sección: Información General
        info_general = []
        info_general.append(html.addParagraph(f"Localización: {localidad}, {pais}"))
        info_general.append(html.addParagraph(f"Longitud: {longitud.text} {longitud.get('unidades')}"))
        info_general.append(html.addParagraph(f"Anchura: {anchura.text} {anchura.get('unidades')}"))
        info_general.append(html.addParagraph(f"Fecha de la carrera: {fecha}"))
        info_general.append(html.addParagraph(f"Hora de inicio: {hora_inicio}"))
        info_general.append(html.addParagraph(f"Número de vueltas: {numero_vueltas}"))
        info_general.append(html.addParagraph(f"Patrocinador principal: {patrocinador}"))
        
        documento.append(html.addSection("Información General", "\n".join(info_general)))
        
        # Sección: Coordenadas de Origen
        coord_content = []
        coord_content.append(html.addParagraph(f"Longitud: {longitud_geo}°"))
        coord_content.append(html.addParagraph(f"Latitud: {latitud_geo}°"))
        coord_content.append(html.addParagraph(f"Altitud: {altitud_geo} metros"))
        
        documento.append(html.addSection("Coordenadas de Origen", "\n".join(coord_content)))
        
        # Sección: Referencias
        if referencias:
            refs_content = []
            refs_content.append("<ul>")
            for ref in referencias:
                enlace = html.addLink(ref.text, ref.text)
                refs_content.append(f"    <li>{enlace}</li>")
            refs_content.append("</ul>")
            refs_html = "\n".join(refs_content)
            documento.append(html.addSection("Referencias", refs_html))
        
        # Sección: Multimedia
        multimedia_content = []
        if fotos:
            multimedia_content.append("<h3>Fotografías</h3>")
            for foto in fotos:
                # Obtener el nombre del archivo sin extensión
                foto_nombre = foto.text.split('/')[-1]
                # Remover la extensión
                if '.' in foto_nombre:
                    foto_nombre = foto_nombre.rsplit('.', 1)[0]
                foto_nombre = foto_nombre.replace('_', ' ').title()
                multimedia_content.append(html.addImage(foto.text, f"Fotografía del circuito: {foto_nombre}", foto_nombre))
        
        if videos:
            multimedia_content.append("<h3>Videos</h3>")
            for video in videos:
                # Obtener el nombre del archivo sin extensión
                video_nombre = video.text.split('/')[-1]
                # Remover la extensión
                if '.' in video_nombre:
                    video_nombre = video_nombre.rsplit('.', 1)[0]
                video_nombre = video_nombre.replace('_', ' ').title()
                multimedia_content.append(html.addVideo(video.text, f"Video del circuito: {video_nombre}"))
        
        if multimedia_content:
            documento.append(html.addSection("Multimedia", "\n".join(multimedia_content)))
        
        # Sección: Resultados
        resultados_content = []
        resultados_content.append(html.addParagraph(f"Vencedor: {nombre_vencedor}"))
        resultados_content.append(html.addParagraph(f"Tiempo de carrera: {tiempo_carrera}"))
        
        # Clasificación mundial
        if clasificados:
            resultados_content.append("<h3>Clasificación Mundial</h3>")
            clasificacion_datos = []
            for clasificado in clasificados:
                posicion = clasificado.get('posicion')
                nombre = clasificado.find('c:nombrePilotoClasificado', namespace).text
                puntos = clasificado.find('c:puntosPiloto', namespace).text
                clasificacion_datos.append((f"Posición {posicion}", f"{nombre} - {puntos} puntos"))
            
            resultados_content.append(html.addDefinitionList(clasificacion_datos))
        
        documento.append(html.addSection("Resultados", "\n".join(resultados_content)))
        
        # Cerrar main
        documento.append(html.addMainClose())
        
        # Cerrar body y html
        documento.append(html.addBodyClose())
        documento.append(html.addHtmlClose())
        
        # Generar documento completo
        documento_completo = "\n".join(documento)
        
        # Escribir archivo HTML
        html.escribir(nombreHTML, documento_completo)
        
    except FileNotFoundError:
        print(f"Error: No se encontró el archivo {archivoXML}")
    except ET.ParseError as e:
        print(f"Error al parsear el archivo XML: {e}")
    except Exception as e:
        print(f"Error inesperado: {e}")

if __name__ == "__main__":
    main()