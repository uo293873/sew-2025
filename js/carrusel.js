class Carrusel {
    #busqueda;
    #actual;
    #maximo;

    constructor() {
        this.#busqueda = "MotoGP, Sepang International Circuit";
        this.#actual = 0;
        this.#maximo = 4;

        this.#getFotografias();
    }

    #getFotografias() {
        $.ajax({
            dataType: "json",
            url: "https://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?",
            method: 'GET',
            data: {
                tags: this.#busqueda,
                tagmode: "all",
                format: "json"
            },
            success: (data) => {
                for(let foto of data.items) {
                    foto.url_z = foto.media.m.replace('_m.', '_z.');
                    
                }
                this.#procesarJSONFotografias(data);
                
            }
        });
    }

    #procesarJSONFotografias(datos) {
        if (datos.items) {
            const fotos = [];
            
            $.each(datos.items, (i, item) => {
                if (i < 5)
                    fotos.push(item);
            });
            
            this.#mostrarFotografias(fotos);
        }
    }

    #mostrarFotografias(fotos) {
        if (fotos.length > 0) {
            const foto = fotos[this.#actual];
            
            if (foto.url_z) {
                const $articulo = $('<article></article>');
                
                const $h2 = $('<h2></h2>').text('Imágenes del circuito de Sepang');
                
                const $img = $('<img>')
                    .attr('src', foto.url_z)
                    .attr('alt', foto.title);
                
                $articulo.append($h2);
                $articulo.append($img);
                
                $('main').append($articulo);
                
                setInterval(() => this.#cambiarFotografia(fotos), 3000);
            }
        }
    }

    #cambiarFotografia(fotos) {
        this.#actual++;
        
        if (this.#actual > this.#maximo)
            this.#actual = 0;
        
        const foto = fotos[this.#actual];
        
        if (foto.url_z) {
            $('main article img')
                .attr('src', foto.url_z)
                .attr('alt', foto.title);
        }
    }
}
