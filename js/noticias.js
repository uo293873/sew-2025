class Noticias {
    #busqueda;
    #url;

    constructor() {
        this.#busqueda = "Campeonato del Mundo de MotoGP";
        this.#url = "https://api.thenewsapi.com/v1/news/all";

        this.#buscar();
    }

    #buscar() {
        const apiKey = "jGl31VfDS0nCHAUuLsu1i0zbHdWgGsR1Hf1rUT1u";
        const urlCompleta = this.#url + "?api_token=" + apiKey + "&search=" + this.#busqueda + "&language=es";
        
        fetch(urlCompleta)
            .then(response => {
                return response.json();
            })
            .then(datos => {
                this.#procesarInformacion(datos);
            });
    }

    #procesarInformacion(datos) {
        if (datos.data) {
            const main = document.querySelector('main');
            const section = document.createElement('section');
            const h2 = document.createElement('h2');
            h2.textContent = 'Noticias de MotoGP';
            section.appendChild(h2);
            
            for (let i = 0; i < datos.data.length; i++) {
                const noticia = datos.data[i];
                
                const article = document.createElement('article');
                
                // Titular
                const h3 = document.createElement('h3');
                h3.textContent = noticia.title;
                article.appendChild(h3);
                
                // Entradilla
                if (noticia.description) {
                    const pDescripcion = document.createElement('p');
                    pDescripcion.textContent = noticia.description;
                    article.appendChild(pDescripcion);
                }
                
                // Fuente
                const pFuente = document.createElement('p');
                pFuente.textContent = 'Fuente: ' + (noticia.source || 'Desconocida');
                article.appendChild(pFuente);
                
                // Enlace
                if (noticia.url) {
                    const enlace = document.createElement('a');
                    enlace.setAttribute('href', noticia.url);
                    enlace.textContent = 'Leer noticia completa';
                    article.appendChild(enlace);
                }
                
                section.appendChild(article);
            }
            
            main.appendChild(section);
        }
    }
}
