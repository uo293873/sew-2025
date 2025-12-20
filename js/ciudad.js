class Ciudad {
    #nombre;
    #pais;
    #gentilicio;
    #poblacion;
    #coordenadas;

    constructor(nombre, pais, gentilicio) {
        this.#nombre = nombre;
        this.#pais = pais;
        this.#gentilicio = gentilicio;
        this.#poblacion = null;
        this.#coordenadas = {
            latitud: null,
            longitud: null
        };
    }

    #rellenarAtributos() {
        this.#poblacion = 2075600;
        this.#coordenadas.latitud = 3.1390;
        this.#coordenadas.longitud = 101.6869;
    }

    #getNombre() {
        return this.#nombre;
    }

    #getPais() {
        return this.#pais;
    }

    #getInformacionSecundaria() {
        const ul = document.createElement('ul');
        
        const liGentilicio = document.createElement('li');
        liGentilicio.textContent = 'Gentilicio: ' + this.#gentilicio;
        ul.appendChild(liGentilicio);
        
        const liPoblacion = document.createElement('li');
        liPoblacion.textContent = 'Población: ' + this.#poblacion;
        ul.appendChild(liPoblacion);
        
        return ul;
    }

    mostrarInformacionCiudad() {
        this.#rellenarAtributos();
        
        const section = document.querySelector('section');
        
        const article = document.createElement('article');
        
        const h3 = document.createElement('h3');
        h3.textContent = 'Información de la Ciudad';
        article.appendChild(h3);
        
        const pCiudad = document.createElement('p');
        pCiudad.textContent = 'Ciudad: ' + this.#getNombre();
        article.appendChild(pCiudad);
        
        const pPais = document.createElement('p');
        pPais.textContent = 'País: ' + this.#getPais();
        article.appendChild(pPais);
        
        article.appendChild(this.#getInformacionSecundaria());
        
        const pCoords = document.createElement('p');
        pCoords.textContent = 'Coordenadas del centro de ' + this.#nombre + ':';
        article.appendChild(pCoords);
        
        const ul = document.createElement('ul');
        
        const liLatitud = document.createElement('li');
        liLatitud.textContent = 'Latitud: ' + this.#coordenadas.latitud;
        ul.appendChild(liLatitud);
        
        const liLongitud = document.createElement('li');
        liLongitud.textContent = 'Longitud: ' + this.#coordenadas.longitud;
        ul.appendChild(liLongitud);
        
        article.appendChild(ul);
        
        section.appendChild(article);
    }

    getMeteorologiaCarrera() {
        $.ajax({
            dataType: "json",
            url: "https://archive-api.open-meteo.com/v1/archive",
            method: 'GET',
            data: {
                latitude: this.#coordenadas.latitud,
                longitude: this.#coordenadas.longitud,
                start_date: "2025-10-26",
                end_date: "2025-10-26",
                hourly: "temperature_2m,apparent_temperature,precipitation,relative_humidity_2m,wind_speed_10m,wind_direction_10m",
                daily: "sunrise,sunset",
                timezone: "auto"
            },
            success: (datos) => {
                this.#procesarJSONCarrera(datos);
            }
        });
    }

    #procesarJSONCarrera(datos) {
        const section = document.querySelector('section');
        
        const article = document.createElement('article');
        
        const h3 = document.createElement('h3');
        h3.textContent = 'Meteorología del Día de la Carrera (26 de octubre de 2025) - 8:00 AM';
        article.appendChild(h3);
        
        // Datos diarios
        if (datos.daily) {
            const pSalida = document.createElement('p');
            pSalida.innerHTML = 'Salida del sol: ' + this.#formatearHora(datos.daily.sunrise[0]);
            article.appendChild(pSalida);
            
            const pPuesta = document.createElement('p');
            pPuesta.innerHTML = 'Puesta del sol: ' + this.#formatearHora(datos.daily.sunset[0]);
            article.appendChild(pPuesta);
        }
        
        // Buscar el índice correspondiente a las 8:00
        if (datos.hourly && datos.hourly.time) {
            let indice8AM = -1;
            for (let i = 0; i < datos.hourly.time.length; i++) {
                const hora = datos.hourly.time[i].split('T')[1];
                if (hora === '08:00') {
                    indice8AM = i;
                    break;
                }
            }
            
            if (indice8AM !== -1) {
                const pTemp = document.createElement('p');
                pTemp.innerHTML = 'Temperatura: ' + datos.hourly.temperature_2m[indice8AM] + ' °C';
                article.appendChild(pTemp);
                
                const pSens = document.createElement('p');
                pSens.innerHTML = 'Sensación térmica: ' + datos.hourly.apparent_temperature[indice8AM] + ' °C';
                article.appendChild(pSens);
                
                const pLluvia = document.createElement('p');
                pLluvia.innerHTML = 'Lluvia: ' + datos.hourly.precipitation[indice8AM] + ' mm';
                article.appendChild(pLluvia);
                
                const pHumedad = document.createElement('p');
                pHumedad.innerHTML = 'Humedad relativa: ' + datos.hourly.relative_humidity_2m[indice8AM] + ' %';
                article.appendChild(pHumedad);
                
                const pViento = document.createElement('p');
                pViento.innerHTML = 'Velocidad del viento: ' + datos.hourly.wind_speed_10m[indice8AM] + ' km/h';
                article.appendChild(pViento);
                
                const pDirViento = document.createElement('p');
                pDirViento.innerHTML = 'Dirección del viento: ' + datos.hourly.wind_direction_10m[indice8AM] + ' °';
                article.appendChild(pDirViento);
            }
        }
        
        section.appendChild(article);
    }

    getMeteorologiaEntrenos() {
        $.ajax({
            dataType: "json",
            url: "https://archive-api.open-meteo.com/v1/archive",
            method: 'GET',
            data: {
                latitude: this.#coordenadas.latitud,
                longitude: this.#coordenadas.longitud,
                start_date: "2025-10-23",
                end_date: "2025-10-25",
                hourly: "temperature_2m,precipitation,wind_speed_10m,relative_humidity_2m",
                timezone: "auto"
            },
            success: (datos) => {
                this.#procesarJSONEntrenos(datos);
            }
        });
    }

    #procesarJSONEntrenos(datos) {
        const section = document.querySelector('section');
        
        // Crear article para datos de entrenamientos
        const article = document.createElement('article');
        
        const h3 = document.createElement('h3');
        h3.textContent = 'Meteorología de los Días de Entrenamientos (23-25 de octubre de 2025)';
        article.appendChild(h3);
        
        if (datos.hourly && datos.hourly.time) {
            // Agrupar datos por día usando arrays
            const dia1Temp = [];
            const dia1Lluvia = [];
            const dia1Viento = [];
            const dia1Humedad = [];
            
            const dia2Temp = [];
            const dia2Lluvia = [];
            const dia2Viento = [];
            const dia2Humedad = [];
            
            const dia3Temp = [];
            const dia3Lluvia = [];
            const dia3Viento = [];
            const dia3Humedad = [];
            
            let fecha1 = '';
            let fecha2 = '';
            let fecha3 = '';
            
            let idx1 = 0;
            let idx2 = 0;
            let idx3 = 0;
            
            for (let i = 0; i < datos.hourly.time.length; i++) {
                const tiempo = datos.hourly.time[i];
                const fecha = tiempo.split('T')[0];
                
                if (fecha1 === '') {
                    fecha1 = fecha;
                }
                
                if (fecha === fecha1) {
                    dia1Temp[idx1] = datos.hourly.temperature_2m[i];
                    dia1Lluvia[idx1] = datos.hourly.precipitation[i];
                    dia1Viento[idx1] = datos.hourly.wind_speed_10m[i];
                    dia1Humedad[idx1] = datos.hourly.relative_humidity_2m[i];
                    idx1++;
                } else if (fecha2 === '' || fecha === fecha2) {
                    if (fecha2 === '') {
                        fecha2 = fecha;
                    }
                    dia2Temp[idx2] = datos.hourly.temperature_2m[i];
                    dia2Lluvia[idx2] = datos.hourly.precipitation[i];
                    dia2Viento[idx2] = datos.hourly.wind_speed_10m[i];
                    dia2Humedad[idx2] = datos.hourly.relative_humidity_2m[i];
                    idx2++;
                } else {
                    if (fecha3 === '') {
                        fecha3 = fecha;
                    }
                    dia3Temp[idx3] = datos.hourly.temperature_2m[i];
                    dia3Lluvia[idx3] = datos.hourly.precipitation[i];
                    dia3Viento[idx3] = datos.hourly.wind_speed_10m[i];
                    dia3Humedad[idx3] = datos.hourly.relative_humidity_2m[i];
                    idx3++;
                }
            }
            
            // Crear tabla con medias
            const table = document.createElement('table');
            
            const caption = document.createElement('caption');
            caption.textContent = 'Promedios Diarios de Entrenamientos';
            table.appendChild(caption);
            
            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');
            
            const thDia = document.createElement('th');
            thDia.setAttribute('scope', 'col');
            thDia.setAttribute('id', 'dia');
            thDia.textContent = 'Día';
            headerRow.appendChild(thDia);
            
            const thTemp = document.createElement('th');
            thTemp.setAttribute('scope', 'col');
            thTemp.setAttribute('id', 'temp');
            thTemp.textContent = 'Temp Media (°C)';
            headerRow.appendChild(thTemp);
            
            const thLluvia = document.createElement('th');
            thLluvia.setAttribute('scope', 'col');
            thLluvia.setAttribute('id', 'lluvia');
            thLluvia.textContent = 'Lluvia Media (mm)';
            headerRow.appendChild(thLluvia);
            
            const thViento = document.createElement('th');
            thViento.setAttribute('scope', 'col');
            thViento.setAttribute('id', 'viento');
            thViento.textContent = 'Vel Viento Media (km/h)';
            headerRow.appendChild(thViento);
            
            const thHumedad = document.createElement('th');
            thHumedad.setAttribute('scope', 'col');
            thHumedad.setAttribute('id', 'humedad');
            thHumedad.textContent = 'Humedad Media (%)';
            headerRow.appendChild(thHumedad);
            
            thead.appendChild(headerRow);
            table.appendChild(thead);
            
            const tbody = document.createElement('tbody');
            
            // Crear fila para día 1
            this.#crearFilaDia(tbody, fecha1, dia1Temp, dia1Lluvia, dia1Viento, dia1Humedad, 'dia0');
            
            // Crear fila para día 2
            this.#crearFilaDia(tbody, fecha2, dia2Temp, dia2Lluvia, dia2Viento, dia2Humedad, 'dia1');
            
            // Crear fila para día 3
            this.#crearFilaDia(tbody, fecha3, dia3Temp, dia3Lluvia, dia3Viento, dia3Humedad, 'dia2');
            
            table.appendChild(tbody);
            article.appendChild(table);
            section.appendChild(article);
        }
    }

    #calcularMedia(datos) {
        let suma = 0;
        for (let i = 0; i < datos.length; i++) {
            suma = suma + datos[i];
        }
        return (suma / datos.length).toFixed(2);
    }

    #formatearHora(fechaISO) {
        const fecha = new Date(fechaISO);
        const horas = ('0' + fecha.getHours()).slice(-2);
        const minutos = ('0' + fecha.getMinutes()).slice(-2);
        return horas + ':' + minutos;
    }

    #crearFilaDia(tbody, fecha, temp, lluvia, viento, humedad, idDia) {
        const tempMedia = this.#calcularMedia(temp);
        const lluviaMedia = this.#calcularMedia(lluvia);
        const vientoMedia = this.#calcularMedia(viento);
        const humedadMedia = this.#calcularMedia(humedad);
        
        const row = document.createElement('tr');
        
        const thFecha = document.createElement('th');
        thFecha.setAttribute('scope', 'row');
        thFecha.setAttribute('id', idDia);
        thFecha.textContent = fecha;
        row.appendChild(thFecha);
        
        const tdTemp = document.createElement('td');
        tdTemp.setAttribute('headers', 'temp ' + idDia);
        tdTemp.textContent = tempMedia;
        row.appendChild(tdTemp);
        
        const tdLluvia = document.createElement('td');
        tdLluvia.setAttribute('headers', 'lluvia ' + idDia);
        tdLluvia.textContent = lluviaMedia;
        row.appendChild(tdLluvia);
        
        const tdViento = document.createElement('td');
        tdViento.setAttribute('headers', 'viento ' + idDia);
        tdViento.textContent = vientoMedia;
        row.appendChild(tdViento);
        
        const tdHumedad = document.createElement('td');
        tdHumedad.setAttribute('headers', 'humedad ' + idDia);
        tdHumedad.textContent = humedadMedia;
        row.appendChild(tdHumedad);
        
        tbody.appendChild(row);
    }
}
