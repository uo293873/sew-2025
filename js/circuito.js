class Circuito {
    constructor() {
        this.#comprobarApiFile();
        this.#registrarEvento();
    }

    #registrarEvento() {
        const inputFile = document.querySelector('input[type="file"]');
        inputFile.addEventListener('change', (evento) => {
            this.#leerArchivoHTML(evento.target.files);
        });
    }

    #comprobarApiFile() {
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            //El navegador soporta el API File
        } else {
            //El navegador NO soporta el API File
            const p = document.createElement('p');
            p.textContent = "¡¡¡ Este navegador NO soporta el API File y este programa puede no funcionar correctamente !!!";
            document.body.appendChild(p);
        }
    }

    #leerArchivoHTML(files) {
        const archivo = files[0];
        
        if (!archivo) {
            return;
        }

        const lector = new FileReader();
        
        lector.onload = (evento) => {
            const contenido = evento.target.result;
            this.#procesarHTML(contenido);
        };
        
        lector.readAsText(archivo);
    }

    #procesarHTML(contenidoHTML) {
        const parser = new DOMParser();
        const documentoHTML = parser.parseFromString(contenidoHTML, 'text/html');
        const mainActual = document.querySelector('main');
        const seccionHTML = mainActual.querySelector('section:first-of-type');
        const secciones = documentoHTML.querySelectorAll('main > section');
        const seccionContenedora = document.createElement('section');
        
        secciones.forEach(seccion => {
            seccionContenedora.appendChild(seccion);
        });
        
        if (seccionHTML) {
            mainActual.insertBefore(seccionContenedora, seccionHTML);
            seccionHTML.remove();
        }
    }
}

class CargadorSVG {
    constructor() {
        this.#registrarEvento();
    }

    #registrarEvento() {
        document.querySelector('input[type="file"][accept=".svg"]').addEventListener('change', (evento) => {
            this.#leerArchivoSVG(evento.target.files);
        });
    }

    #leerArchivoSVG(files) {
        const archivo = files[0];
        
        if (!archivo) {
            return;
        }

        const lector = new FileReader();
        
        lector.onload = (evento) => {
            const contenido = evento.target.result;
            this.#insertarSVG(contenido);
        };
        
        lector.readAsText(archivo);
    }

    #insertarSVG(contenidoSVG) {
        const parser = new DOMParser();
        const documentoSVG = parser.parseFromString(contenidoSVG, 'image/svg+xml');
        const svg = documentoSVG.querySelector('svg');

        const section = document.createElement('section');
        
        const h2 = document.createElement('h2');
        h2.textContent = 'Perfil de Altimetría del Circuito';
        section.appendChild(h2);
        
        section.appendChild(svg);
        
        const main = document.querySelector('main');
        const seccionSVG = main.querySelector('section:nth-of-type(2)');
        
        if (seccionSVG) {
            main.insertBefore(section, seccionSVG);
            seccionSVG.remove();
        }
    }
}

class CargadorKML {
    constructor() {
        this.mapa = null;
        this.coordenadasOrigen = null;
        this.coordenadasTramos = [];
        this.#registrarEvento();
    }

    #registrarEvento() {
        document.querySelector('input[type="file"][accept=".kml"]').addEventListener('change', (evento) => {
            this.#leerArchivoKML(evento.target.files);
        });
    }

    #leerArchivoKML(files) {
        const archivo = files[0];
        
        if (!archivo) {
            return;
        }

        const lector = new FileReader();
        
        lector.onload = (evento) => {
            const contenido = evento.target.result;
            this.#procesarKML(contenido);
        };
        
        lector.readAsText(archivo);
    }

    #procesarKML(contenidoKML) {
        const parser = new DOMParser();
        const documentoKML = parser.parseFromString(contenidoKML, 'text/xml');
        
        const puntoOrigen = documentoKML.querySelector('Point coordinates');
        if (puntoOrigen) {
            const coords = puntoOrigen.textContent.trim().split(',');
            this.coordenadasOrigen = {
                lng: parseFloat(coords[0]),
                lat: parseFloat(coords[1])
            };
        }
        
        const lineString = documentoKML.querySelector('LineString coordinates');
        if (lineString) {
            const coordsTexto = lineString.textContent.trim();
            const lineas = coordsTexto.split('\n');
            
            this.coordenadasTramos = lineas
                .map(linea => linea.trim())
                .filter(linea => linea.length > 0)
                .map(linea => {
                    const coords = linea.split(',');
                    return {
                        lng: parseFloat(coords[0]),
                        lat: parseFloat(coords[1])
                    };
                });
        }
        
        this.#insertarCapaKML();
    }

    async #insertarCapaKML() {
        const seccionKML = document.querySelector('main > section:last-of-type');
        
        if (seccionKML) {
            const parrafo = seccionKML.querySelector('p');
            const input = seccionKML.querySelector('input');
            if (parrafo) parrafo.remove();
            if (input) input.remove();
            
            const divMapa = document.createElement('div');
            seccionKML.appendChild(divMapa);
            
            // Importar las librerías necesarias
            const { Map } = await google.maps.importLibrary("maps");
            const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
            
            this.mapa = new Map(divMapa, {
                center: this.coordenadasOrigen,
                zoom: 15,
                mapTypeId: 'satellite',
                mapId: 'SEPANG_CIRCUIT_MAP'
            });
            
            new AdvancedMarkerElement({
                map: this.mapa,
                position: this.coordenadasOrigen,
                title: 'Línea de Meta - Sepang International Circuit'
            });
            
            const polilinea = new google.maps.Polyline({
                path: this.coordenadasTramos,
                geodesic: true,
                strokeColor: '#FF6600',
                strokeOpacity: 1.0,
                strokeWeight: 4
            });
            
            polilinea.setMap(this.mapa);
        }
    }
}