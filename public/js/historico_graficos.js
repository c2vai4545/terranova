document.addEventListener('DOMContentLoaded', function() {
    const lecturasDataElement = document.getElementById('lecturas-data');
    if (!lecturasDataElement) {
        console.error('Element with ID "lecturas-data" not found.');
        return;
    }
    const lecturasData = JSON.parse(lecturasDataElement.dataset.lecturas);

    if (lecturasData && lecturasData.length > 0) {
        lecturasData.forEach((lectura, index) => {
            const ctx = document.getElementById('grafico_' + index).getContext('2d');
            const labels = lectura.data.map(dato => dato.fechaLectura + ' ' + dato.horaLectura);
            const data = lectura.data.map(dato => dato.lectura);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: lectura.tipoNombre,
                        data: data,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    }
});