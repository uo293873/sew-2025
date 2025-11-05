class Memoria {
    constructor() {
        // Inicialización de los atributos
        this.tablero_bloqueado = true;
        this.primera_carta = null;
        this.segunda_carta = null;
        
        // Barajado de las cartas
        this.barajarCartas();
        
        // Desbloqueo del tablero
        this.tablero_bloqueado = false;
        
        this.cronometro = new Cronometro();
        this.cronometro.arrancar();
    }

    voltearCarta(carta) {
        // Si carta deshabilitada, carta volteada o tablero bloqueado no hacer nada
        if (carta.dataset.estado === "revelada" ||
            carta.dataset.estado === "volteada" ||
            this.tablero_bloqueado) {
            return;
        }

        carta.dataset.estado = "volteada";
        
        // Primera carta
        if (this.primera_carta === null) {
            this.primera_carta = carta;
            return;
        }
        // Segunda carta
        this.segunda_carta = carta;
        this.comprobarPareja();
    }

    barajarCartas() {
        const main = document.querySelector('main');
        const cartas = main.querySelectorAll('article');

        for (let i = 0; i < cartas.length; i++) {
            const j = Math.floor(Math.random() * (i + 1));
            main.appendChild(cartas[j]);
        }
    }

    reiniciarAtributos() {
        this.primera_carta = null;
        this.segunda_carta = null;
        this.tablero_bloqueado = false;
    }

    deshabilitarCartas(){
        this.primera_carta.dataset.estado = "revelada";
        this.segunda_carta.dataset.estado = "revelada";
        this.comprobarJuego();
        this.reiniciarAtributos();
    }

    comprobarJuego(){
        const cartasReveladas = document.querySelectorAll('main article[data-estado="revelada"]');
        if (cartasReveladas.length === document.querySelectorAll('main article').length){
            this.cronometro.parar();
        }
    }

    cubrirCartas(){
        this.tablero_bloqueado = true;
        setTimeout(() => {
            this.primera_carta.dataset.estado = null;
            this.segunda_carta.dataset.estado = null;
            this.reiniciarAtributos();
        }, 1500);
    }

    comprobarPareja() {
        const img1 = this.primera_carta.children[1];
        const img2 = this.segunda_carta.children[1];
        
        const src1 = img1.getAttribute('src');
        const src2 = img2.getAttribute('src');
        
        src1 === src2 ? this.deshabilitarCartas() : this.cubrirCartas();
    }

}
