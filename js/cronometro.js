class Cronometro {
    constructor() {
        this.tiempo = 0;
    }

    arrancar() {
        if (this.corriendo) {
            return;
        }

        try {
            // Intenta usar Temporal
            this.inicio = Temporal.Now.instant();
        } catch (error) {
            // Si Temporal da error usa Date
            this.inicio = new Date();
        }

        this.corriendo = setInterval(this.actualizar.bind(this), 100);
    }

    actualizar() {
        let ahora;
        try {
            ahora = Temporal.Now.instant().epochMilliseconds;
        } catch (error) {
            ahora = new Date().getTime();
        }
        
        const inicio = this.inicio.epochMilliseconds || this.inicio.getTime();
        this.tiempo = ahora - inicio;

        this.mostrar();
    }

    mostrar() {
        const minutos = parseInt(this.tiempo / 60000);
        const segundos = parseInt((this.tiempo % 60000) / 1000);
        const decimas = parseInt((this.tiempo % 1000) / 100);

        // Formatea con ceros a la izquierda usando padStart
        const minutosStr = minutos.toString().padStart(2, '0');
        const segundosStr = segundos.toString().padStart(2, '0');
        const decimasStr = decimas.toString();
        const tiempoFormateado = `${minutosStr}:${segundosStr}.${decimasStr}`;

        const parrafo = document.querySelector('main p');
        parrafo.textContent = tiempoFormateado;

    }

    parar() {
        clearInterval(this.corriendo);
        this.corriendo = null;
    }

    reiniciar() {
        clearInterval(this.corriendo);
        this.corriendo = null;
        this.tiempo = 0;
        this.mostrar();
    }
}
