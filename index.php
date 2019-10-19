<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="Rodrigo Pereira">
        <meta name="description" content="IMA - BOOTSTRAP FRAMEWORK FRONT-END -">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>IMA - BOOTSTRAP FRAMEWORK FRONT-END -</title>
        <link rel="stylesheet" href="assets/css/fontawesome.css">
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/style.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    </head>
    <body>
        <nav class="navbar text-center navbar-expand-md navbar-dark bg-dark">
            <a class="container" href="#">IMA - BALNEABILIDADE</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbars" aria-controls="navbars" aria-expanded="false" aria-label="Toggle Navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </nav>
        <main class="container" role="main">&nbsp;</main>
        <main class="container" role="main">
            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-6 p-0">
				<label for="business-name" class="control-label">Praias da região do vale do itajai</label>
                <div class="">
				    <select name="ima" id="ima" class="form-control">
                        <option value="">Selecione uma praia</option>
                        <option value="25-44-2019">Meia Praia - Itapema</option>
                        <option value="24-40-2019">Praia Central - Balneário Camboriú</option>
                        <option value="24-41-2019">Praia de Laranjeiras - Balneário Camboriú</option>
                        <option value="26-46-2019">Praia Perequê - Porto Belo</option>
                        <option value="23-39-2019">Praia Brava - Itajaí</option>
                        <option value="23-37-2019">Praia de Cabeçudas - Itajaí</option>
                    </select>
				</div>
			</div>
        </main>
        <main class="container" role="main">
            <div class="starter-template">
                <div class="col-lg-12 row">
                    <canvas id="line-chart"></canvas>
                </div>
            </div>
        </main>
    </body>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/fontawesome.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#ima').on('change', function() {
                if ($('#ima').val() != '') {
                    var ima = $('#ima').val();
                    var dados = ima.split('-');
                    $.ajax({
                        url: "ima.php",
                        async: true,
                        type: "POST",
                        data: { 'municipio': dados[0], 'praia': dados[1], 'ano': dados[2] },
                        cache: false,
                        dataType: "json",
                        crossDomain: true,
                        // contentType: 'application/json',
                        success: function (result) {
                            var points = [],
                                lineT  = [],
                                colorslist = ["lime","blue","cyan","orange","amber","magenta","teal","green","brown","black","grey","navy","white","yellow","pink","red","ruby","iron"];

                            var dataLabels,
                                dataSets,
                                year;
                            $.each(result, function (i, iValue) {
                                var series = [];
                                if (i % 2 == 0) {
                                    var ecoli = [],
                                        dataEcoli = [];

                                    $.each(result[i], function (j, jValue) {
                                        ecoli.push(jValue.ecoli);
                                        dataEcoli.push(jValue.data);
                                    });

                                    series = ecoli;
                                    lineT = dataEcoli;
                                }
                                
                                var colors = '',
                                    point_collect = '';
                                
                                if (series.length != 0) {
                                    point_collect = result[i-1].Ponto_de_Coleta +": "+ result[i-1].Localizacao;
                                    colors = colorslist[i-1];

                                    points.push({
                                        fill: false,
                                        data: series.reverse(),
                                        borderColor: colors,
                                        label: point_collect,
                                    });
                                }
                            });

                            dataLabels = lineT.reverse();
                            dataSets = points;
                            year = dataLabels[0].split('/');

                            new Chart(document.getElementById("line-chart"), {
                                type: 'line',
                                data: {
                                    labels: dataLabels,
                                    datasets: dataSets
                                },
                                options: {
                                    title: {
                                        display: true,
                                        text: 'CIDADE DE '+ result[1].Municipio +' - '+ result[1].Balneario +' - ANO: '+ year[2]
                                    }
                                }
                            });
                        },
                        error:function(jqXHR, textStatus, errorThrown) {
                            alert('Erro ao carregar');
                        }
                    });
                } else {
                    alert('Escolha uma praia.');
                }
            });
		});
	</script>
</html>