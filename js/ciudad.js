class Ciudad {
    constructor(nombre, pais, gentilicio) {
        this.nombre = nombre;
        this.pais = pais;
        this.gentilicio = gentilicio;
        this.poblacion = null;
        this.coordenadas = {
            latitud: null,
            longitud: null
        };
    }

    rellenarAtributos() {
        this.poblacion = 2075600;
        this.coordenadas.latitud = 3.1390;
        this.coordenadas.longitud = 101.6869;
    }

    getNombre() {
        return this.nombre;
    }

    getPais() {
        return this.pais;
    }

    getInformacionSecundaria() {
        return "<ul><li>Gentilicio: " + this.gentilicio + "</li>" +
               "<li>Población: " + this.poblacion + "</li></ul>";
    }

    escribirCoordenadas() {
        document.write("<p>Coordenadas del centro de " + this.nombre + ":</p>");
        document.write("<ul><li>Latitud: " + this.coordenadas.latitud + "</li>" +
                      "<li>Longitud: " + this.coordenadas.longitud + "</li></ul>");
    }
}
