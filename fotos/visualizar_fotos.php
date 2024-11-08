<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="/includes/estilo.css" rel="stylesheet">
    <link rel="icon" href="/includes/logo.ico">
    <title>Visualizar fotos</title>

</head>

<style>
    @media print {

        .header {
            padding-top: 0px;
        }

        .legenda {
            color: black !important;
            background-color: #d7b469 !important;
            white-space: nowrap;
        }

        .legenda-pequena {
            font-size: 16px !important;
        }

        .titulo {
            font-size: 28px !important;
            text-align: center !important;
            /* Centraliza o título */
            margin-left: 0 !important;
            /* Remove a margem esquerda */
            width: 100%;
            /* Garante que o título ocupe toda a largura */
        }

        .logo {
            position: absolute;
            /* Garante o posicionamento absoluto */
            right: 5px;
            /* Posiciona o logo no canto superior direito */
            top: 0px;
            height: 75px;
        }

        .navbar,
        .remover-botao,
        .registros,
        .filtros {
            display: none;
        }

        .card-container {
            height: auto !important;
            /* Ajusta o tamanho para que todos tenham a mesma altura */
            padding: 5px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            /* Distribui o conteúdo dentro do card */
            page-break-inside: avoid;
            /* Evita quebra de página dentro do card */
        }

        .card-container:nth-of-type(8n) {
            page-break-after: always;
        }

        .info {
            margin: 3px 0 !important;
            line-height: 1;
            font-size: 12px !important;
        }

        .foto {
            height: 250px !important;
            width: 250px !important;
        }
    }
</style>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/global_constraints.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/autoload.php';
require_once HOME_DIR . 'componentes/navbar.php';


$con = connect_local_mysqli('gestao_ambiental');

$datade = '';
$dataate = '';
$setor = '';
$subsecao = '';
$local = '';
$ocorrencia = '';
$conforme = '';
$limbauba = '';
$lcastanheira = '';
$lpaubrasil = '';
$totalRegistros = '';

$url_base = "http://gestaoambiental.com.br/fotos/registrar_fotos.php?foto=";

$total = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datade = $_REQUEST['datade'] ?? '';
    $dataate = $_REQUEST['dataate'] ?? '';
    $setor = $_REQUEST['setor'] ?? '';
    $subsecao = $_REQUEST['subsecao'] ?? '';
    $local = $_REQUEST['local'] ?? '';
    $ocorrencia = $_REQUEST['ocorrencia'] ?? '';
    $conforme = $_REQUEST['conforme'] ?? '';
    $limbauba = $_REQUEST['imbauba'] ?? '';
    $lcastanheira = $_REQUEST['castanheira'] ?? '';
    $lpaubrasil = $_REQUEST['paubrasil'] ?? '';
}

?>

<body>

    &nbsp;
    <div id="main" class="container-fluid" style="margin:5px;padding: 0px;">
        <div class="header">
            <div class="logo" style="position: absolute; top: 55px; right: 5px;">
                <img class="logo" src="../includes/logo.ico" alt="Logo" style="max-height: 150px;">
            </div>
            <div class="row justify-content-center align-items-center">
                <div class="col-md-12 mb-2 text-center">
                    <h1 class="titulo">Relatórioooo</h1>
                </div>
            </div>
        </div>
        <div class="row justify-content-center align-items-center filtros">
            <form method="post" action="" id="form1" name="form1" autocomplete="off" class="d-flex flex-wrap justify-content-center">

                <div class="container">
                    <div class="row w-100 justify-content-center align-items-end">
                        <div class="form-group col-md-2">
                            <label for="datade" class="form-label"><strong>De:</strong></label>
                            <input type="date" class="form-control form-control-sm" id="datade" name="datade" value="<?= $datade ?>">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="dataate" class="form-label"><strong>Até:</strong></label>
                            <input type="date" class="form-control form-control-sm" id="dataate" name="dataate" value="<?= $dataate ?>">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="setor"><strong>Setor</strong></label>
                            <select class="form-select ml-2" id="setor" name="setor" data-placeholder="Selecione um setor...">
                                <option value="" disabled <?= empty($setor) ? 'selected' : '' ?>>Selecione um setor...</option>
                                <?php
                                $sql = "SELECT * FROM setores ORDER BY 2 ASC";
                                $resultado = mysqli_query($con, $sql);
                                while ($row = mysqli_fetch_assoc($resultado)) {
                                    $selected = ($row['id'] === $setor) ? 'selected' : '';
                                    echo "<option value='" . $row['id'] . "' $selected>" . $row['setor'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 mt-4">
                            <button type="button" class="btn btn-dark w-100 btn" id="pesquisarBtn" onclick="document.getElementById('form1').submit();">Pesquisar</button>
                        </div>
                    </div>
                    <div class="row w-100 justify-content-center align-items-end">
                        <div class="form-group col-md-2">
                            <label for="subsecao" class="form-label"><strong>Subseção:</strong></label>
                            <select class="form-select ml-2" id="subsecao" name="subsecao" data-placeholder="Selecione uma subseção...">
                                <option value="" disabled <?= empty($subsecao) ? 'selected' : '' ?>>Selecione um subseção...</option>
                                <?php
                                $con = connect_local_mysqli('gestao_ambiental');
                                $sql = "SELECT * FROM subsecoes ORDER BY 2 ASC";
                                $resultado = mysqli_query($con, $sql);
                                while ($row = mysqli_fetch_assoc($resultado)) {
                                    $selected = ($row['id'] === $subsecao) ? 'selected' : '';
                                    echo "<option value='" . $row['id'] . "' $selected>" . $row['subsecao'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="local" class="form-label"><strong>Local:</strong></label>
                            <select class="form-select ml-2" id="local" name="local" data-placeholder="Selecione um local...">
                                <option value="" disabled <?= empty($local) ? 'selected' : '' ?>>Selecione um local...</option>
                                <?php
                                $con = connect_local_mysqli('gestao_ambiental');
                                $sql = "SELECT * FROM local ORDER BY 2 ASC";
                                $resultado = mysqli_query($con, $sql);
                                while ($row = mysqli_fetch_assoc($resultado)) {
                                    $selected = ($row['id'] === $local) ? 'selected' : '';
                                    echo "<option value='" . $row['id'] . "' $selected>" . $row['local'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="ocorrencia" class="form-label"><strong>Ocorrência:</strong></label>
                            <select class="form-select ml-2" id="ocorrencia" name="ocorrencia" data-placeholder="Selecione uma ocorrência...">
                                <option value="" disabled <?= empty($ocorrencia) ? 'selected' : '' ?>>Selecione um ocorrëncia...</option>
                                <?php
                                $con = connect_local_mysqli('gestao_ambiental');
                                $sql = "SELECT * FROM ocorrencia ORDER BY 2 ASC";
                                $resultado = mysqli_query($con, $sql);
                                while ($row = mysqli_fetch_assoc($resultado)) {
                                    $selected = ($row['id'] === $ocorrencia) ? 'selected' : '';
                                    echo "<option value='" . $row['id'] . "' $selected>" . $row['ocorrencia'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 mt-4">
                            <button type="button" class="btn btn-danger w-100 btn" id="resetBtn">Resetar</button>
                        </div>
                    </div>

                    <div class="row w-100 justify-content-center align-items-end">

                        <div class="form-group col-md-2">
                            <label for="imbauba" class="form-label"><strong>Imbaúba:</strong></label>
                            <select class="form-select ml-2" id="imbauba" name="imbauba" data-placeholder="Selecione um item...">
                                <option value="" disabled <?= empty($imbauba) ? 'selected' : '' ?>>Selecione um item...</option>
                                <?php
                                $con = connect_local_mysqli('gestao_ambiental');
                                $sql = "SELECT * FROM lista_imbauba ORDER BY 2 ASC";
                                $resultado = mysqli_query($con, $sql);
                                while ($row = mysqli_fetch_assoc($resultado)) {
                                    $selected = ($row['id'] === $limbauba) ? 'selected' : '';
                                    echo "<option value='" . $row['id'] . "' $selected>" . $row['item'] . " - " . $row['desc_item'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="castanheira" class="form-label"><strong>Castanheira:</strong></label>
                            <select class="form-select ml-2" id="castanheira" name="castanheira" data-placeholder="Selecione um item...">
                                <option value="" disabled <?= empty($castanheira) ? 'selected' : '' ?>>Selecione um item...</option>
                                <?php
                                $con = connect_local_mysqli('gestao_ambiental');
                                $sql = "SELECT * FROM lista_castanheira ORDER BY 2 ASC";
                                $resultado = mysqli_query($con, $sql);
                                while ($row = mysqli_fetch_assoc($resultado)) {
                                    $selected = ($row['id'] === $lcastanheira) ? 'selected' : '';
                                    echo "<option value='" . $row['id'] . "' $selected>" . $row['item'] . " - " . $row['desc_item'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="paubrasil" class="form-label"><strong>Pau-brasil:</strong></label>
                            <select class="form-select ml-2" id="paubrasil" name="paubrasil" data-placeholder="Selecione um item...">
                                <option value="" disabled <?= empty($paubrasil) ? 'selected' : '' ?>>Selecione um item...</option>
                                <?php
                                $con = connect_local_mysqli('gestao_ambiental');
                                $sql = "SELECT * FROM lista_pau_brasil ORDER BY 2 ASC";
                                $resultado = mysqli_query($con, $sql);
                                while ($row = mysqli_fetch_assoc($resultado)) {
                                    $selected = ($row['id'] === $lpaubrasil) ? 'selected' : '';
                                    echo "<option value='" . $row['id'] . "' $selected>" . $row['item'] . " - " . $row['desc_item'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="conforme" class="form-label"><strong>Conforme:</strong></label>
                            <select class="form-select ml-2" id="conforme" name="conforme" data-placeholder="Selecione uma opção...">
                                <option value="" disabled <?= empty($conforme) ? 'selected' : '' ?>>Selecione uma opção...</option>
                                <option value="S">Sim</option>
                                <option value="N">Não</option>
                                <option value="P">Parcial</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <br>
        <div class="row justify-content-center align-items-center">
            <div class="col-md-12">
                <?php
                if (($datade && $dataate) || $setor || $subsecao || $local || $ocorrencia || $conforme || $limbauba || $lcastanheira || $lpaubrasil) {

                    $filtroData = '';
                    $filtroSetor = '';
                    $filtroSubsecao = '';
                    $filtroLocal = '';
                    $filtroOcorrencia = '';
                    $filtroConforme = '';
                    $filtroImbauba = '';
                    $filtroCastanheira = '';
                    $filtroPauBrasil = '';

                    if ($datade && $dataate) {
                        $filtroData = " AND data BETWEEN '$datade' AND '$dataate'";
                    }

                    if ($setor) {
                        $filtroSetor = " AND id_setor = '$setor' ";
                    }

                    if ($subsecao) {
                        $filtroSubsecao = " AND id_subsecao = '$subsecao' ";
                    }

                    if ($local) {
                        $filtroLocal = " AND id_local = '$local' ";
                    }

                    if ($ocorrencia) {
                        $filtroOcorrencia = " AND id_ocorrencia = '$ocorrencia' ";
                    }

                    if ($conforme) {
                        $filtroConforme = " AND conforme = '$conforme' ";
                    }

                    if ($limbauba) {
                        $filtroImbauba = " AND limbauba = '$limbauba' ";
                    }

                    if ($lcastanheira) {
                        $filtroCastanheira = " AND lcastanheira = '$lcastanheira' ";
                    }

                    if ($lpaubrasil) {
                        $filtroPauBrasil = " AND lpaubrasil = '$lpaubrasil' ";
                    }

                    $sql = "SELECT nome_arquivo, 
                                setor, 
                                subsecao, 
                                local, 
                                ocorrencia,
                                data, 
                                conforme,
                                lista_castanheira.item as item_cast,
                                lista_imbauba.item as item_imb,
                                lista_pau_brasil.item as item_pau,
                                observacao
                            FROM fotos
                            LEFT JOIN setores on id_setor = setores.id
                            LEFT JOIN subsecoes on id_subsecao = subsecoes.id
                            LEFT JOIN local on id_local = local.id
                            LEFT JOIN ocorrencia on id_ocorrencia = ocorrencia.id
                            LEFT JOIN lista_castanheira on lcastanheira = lista_castanheira.id
                            LEFT JOIN lista_imbauba on limbauba = lista_imbauba.id
                            LEFT JOIN lista_pau_brasil on lpaubrasil = lista_pau_brasil.id
                            WHERE 1=1"
                        . $filtroData
                        . $filtroSetor
                        . $filtroSubsecao
                        . $filtroLocal
                        . $filtroOcorrencia
                        . $filtroConforme
                        . $filtroImbauba
                        . $filtroCastanheira
                        . $filtroPauBrasil;

                    $resultado = mysqli_query($con, $sql);

                    if (!$resultado) {
                        $e = mysqli_error($con);
                        echo "Erro ao executar a consulta de total: " . $e;
                    }

                    $sqlT = "SELECT COUNT(1) AS TOTAL FROM FOTOS WHERE 1=1"
                        . $filtroData
                        . $filtroSetor
                        . $filtroSubsecao
                        . $filtroLocal
                        . $filtroOcorrencia
                        . $filtroConforme
                        . $filtroImbauba
                        . $filtroCastanheira
                        . $filtroPauBrasil;
                    $resultadoT = mysqli_query($con, $sqlT);

                    if (!$resultadoT) {
                        $e = mysqli_error($con);
                        echo "Erro ao executar a consulta de total: " . $e;
                    } else {
                        $total = mysqli_fetch_assoc($resultadoT);
                        if ($total && isset($total['TOTAL'])) {
                            $totalRegistros = $total['TOTAL'];
                        } else {
                            $totalRegistros = 0;
                        }
                    }

                    echo '<table class="registros" style="width: 100%;" role="grid">
                            <tbody>
                                <tr role="row">
                                    <td role="gridcell">
                                        <span class="titulo"><b>' . $totalRegistros . ' Registros encontrados</b></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>';

                    $imagens = [];
                    $legendas = [];
                    $setor = [];
                    $subsecao = [];
                    $local = [];
                    $ocorrencia = [];
                    $data = [];
                    $conforme = [];
                    $corconforme = [];
                    $castanheira = [];
                    $imbauba = [];
                    $paubrasil = [];
                    $observacao = [];

                    if ($resultado != null) {
                        while ($row = mysqli_fetch_assoc($resultado)) {

                            if (isset($row['nome_arquivo'])) {
                                $imagens[] = $row['nome_arquivo'];
                            } else {
                                $imagens[] = '';
                            }

                            if (isset($row['setor'])) {
                                $legendas[] = $row['setor'] . (!empty($row['subsecao']) ? ' - ' . $row['subsecao'] : '');
                            } else {
                                $legendas[] = '';
                            }

                            if (isset($row['setor'])) {
                                $setor[] = $row['setor'];
                            } else {
                                $setor[] = '';
                            }

                            if (isset($row['subsecao'])) {
                                $subsecao[] = $row['subsecao'];
                            } else {
                                $subsecao[] = '';
                            }

                            if (isset($row['local'])) {
                                $local[] = $row['local'];
                            } else {
                                $local[] = '';
                            }

                            if (isset($row['ocorrencia'])) {
                                $ocorrencia[] = $row['ocorrencia'];
                            } else {
                                $ocorrencia[] = '';
                            }

                            if (isset($row['data'])) {
                                $data[] = $row['data'];
                            } else {
                                $data[] = '';
                            }

                            if (isset($row['conforme'])) {
                                if ($row['conforme'] == 'S') {
                                    $conforme[] = 'Sim';
                                    $corconforme[] = 'green';
                                } else if ($row['conforme'] == 'N') {
                                    $conforme[] = 'Não';
                                    $corconforme[] = 'red';
                                } else if ($row['conforme'] == 'P') {
                                    $conforme[] = 'Parcial';
                                    $corconforme[] = 'orange';
                                }
                            } else {
                                $conforme[] = '';
                            }

                            if (isset($row['item_cast'])) {
                                $castanheira[] = "Item " . $row['item_cast'];
                            } else {
                                $castanheira[] = '';
                            }

                            if (isset($row['item_imb'])) {
                                $imbauba[] = "Item " . $row['item_imb'];
                            } else {
                                $imbauba[] = '';
                            }

                            if (isset($row['item_pau'])) {
                                $paubrasil[] = "Item " . $row['item_pau'] . (!empty($row['desc_pau']) ? ' - ' . $row['desc_pau'] : '');
                            } else {
                                $paubrasil[] = '';
                            }

                            if (isset($row['observacao'])) {
                                $observacao[] = $row['observacao'];
                            } else {
                                $observacao[] = '';
                            }
                        }
                    }

                    echo '<div class="container">';
                    echo '<div class="row" id="sortable-container" >';

                    $imagensVisiveis = $imagens;
                    $contador = 0;

                    foreach ($imagensVisiveis as $imagem) {

                        echo '<div class="col-3 text-left card-container" data-id="' . $contador . '" data-nome="' . htmlspecialchars($imagem) . '" style="padding: 5px;">';

                        if (isset($legendas[$contador])) {
                            $legendaTexto = $legendas[$contador];
                            // Aplica a classe se a string for maior que 20 caracteres
                            $classeLegenda = (strlen($legendaTexto) > 20) ? ' legenda-pequena' : '';

                            echo '<div class="text-center legenda' . $classeLegenda . '" style="padding: 10px; margin-bottom: 5px; border-radius: 5px; position: relative;">';
                            echo '<b>' . $legendaTexto . '</b>';
                            echo '<i class="bi bi-x remover-botao" onclick="removerCard(this)" style="cursor: pointer; position: absolute; top: 10px; right: 10px;"></i>';
                            echo '</div>';
                        }


                        echo '<div style="text-align: center;">
                            <div style="position: relative; display: inline-block;">
                                <img src="' . $url_base . $imagem . '" alt="' . htmlspecialchars($imagem) . '" class="img-fluid foto" style="max-width: 100%; height: 380px; display: block;" onclick="openPopup(\'' . $url_base . $imagem . '\')">
                                <img src="../includes/logo.ico" alt="Logo" class="logo" style="position: absolute; bottom: 5px; right: 5px; opacity: 0.5; max-height: 30px;">
                            </div>
                        </div>';

                        if (!empty($conforme[$contador]) && $conforme[$contador] !== '-') {
                            echo '<p class="info conforme" style="margin: 5px 0;"><b>Conforme:</b> <span style="color: ' . $corconforme[$contador] . '; font-weight: bold;">' . $conforme[$contador] . '</span></p>';
                        }

                        if (!empty($data[$contador]) && $data[$contador] !== '-') {
                            echo '<p class="info data" style="margin: 5px 0;"><b>Data:</b> ' . banco_para_data($data[$contador]) . '</p>';
                        }

                        if (!empty($local[$contador]) && $local[$contador] !== '-') {
                            echo '<p class="info local" style="margin: 5px 0;"><b>Local:</b> ' . $local[$contador] . '</p>';
                        }

                        if (!empty($imbauba[$contador]) && $imbauba[$contador] !== '-') {
                            echo '<p class="info imbauba" style="margin: 5px 0;"><b>Imbaúba:</b> ' . $imbauba[$contador] . '</p>';
                        }

                        if (!empty($castanheira[$contador]) && $castanheira[$contador] !== '-') {
                            echo '<p class="info castanheira" style="margin: 5px 0;"><b>Castanheira:</b> ' . $castanheira[$contador] . '</p>';
                        }

                        if (!empty($paubrasil[$contador]) && $paubrasil[$contador] !== '-') {
                            echo '<p class="info paubrasil" style="margin: 5px 0;"><b>Pau Brasil:</b> ' . $paubrasil[$contador] . '</p>';
                        }

                        if (!empty($ocorrencia[$contador]) && $ocorrencia[$contador] !== '-') {
                            echo '<p class="info ocorrencia" style="margin: 5px 0;"><b>Evidência:</b> ' . $ocorrencia[$contador] . '</p>';
                        }

                        if (!empty($observacao[$contador]) && $observacao[$contador] !== '-') {
                            echo '<p class="info observacao" style="margin: 5px 0;"><b>Ação corretiva:</b> ' . $observacao[$contador] . '</p>';
                        }



                        echo '</div>';
                        $contador++;
                    }
                    echo '</div>';
                    echo '</div>';
                }

                ?>
            </div>
        </div>

        <footer>Desenvolvido por: Douglas Marcondes.</footer>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

        <script type="text/javascript" charset="utf-8">
            $(document).ready(function() {

                $('#setor, #subsecao, #local, #ocorrencia, #conforme, #castanheira, #imbauba, #paubrasil').select2({
                    placeholder: function() {
                        return $(this).data('placeholder');
                    },
                    allowClear: true,
                    width: '100%'
                });

                $('.select2-selection').css('height', '40px');
                $('.select2-selection__rendered').css({
                    'line-height': '40px',
                    'padding-top': '0px'
                });

            });

            $("#resetBtn").click(function(event) {
                event.preventDefault();


                $('#datade').val("").trigger('change');
                $('#dataate').val("").trigger('change');
                $('#setor').val("").trigger('change');
                $('#subsecao').val("").trigger('change');
                $('#local').val("").trigger('change');
                $('#ocorrencia').val("").trigger('change');
                $('#conforme').val("").trigger('change');
                $('#imbauba').val("").trigger('change');
                $('#castanheira').val("").trigger('change');
                $('#paubrasil').val("").trigger('change');
            });

            function restructureRows() {
                const container = document.getElementById('sortable-container');
                const cards = Array.from(container.querySelectorAll('.card-container'));

                container.innerHTML = '';
                cards.forEach((card, index) => {
                    container.appendChild(card);
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                const sortable = new Sortable(document.getElementById('sortable-container'), {
                    animation: 150,
                    ghostClass: 'ghost',
                    onEnd: function() {
                        restructureRows();
                        let newOrder = [];
                        document.querySelectorAll('.card-container').forEach((el) => {
                            newOrder.push(el.getAttribute('data-id'));
                        });
                    }
                });
            });

            function removerCard(element) {
                var card = element.closest('.card-container');
                var fileName = card.getAttribute('data-nome');
                card.style.display = 'none';
                restructureRows();
            }

            function openPopup(imageSrc) {
                const width = 768;
                const height = 1024;
                const left = (window.screen.width / 2) - (width / 2);
                const top = (window.screen.height / 2) - (height / 2);
                window.open(imageSrc, "_blank", "width=" + width + ",height=" + height + ",top=" + top + ",left=" + left);
            }

            function atualizarLinhas() {
                var container = document.querySelector('.container');
                var rows = container.querySelectorAll('.row');
                var scrollTop = container.scrollTop;
                var currentRow = null;
                var contador = 0;
                rows.forEach(row => {
                    row.remove();
                });
                rows.forEach(row => {
                    var cols = row.querySelectorAll('.card-container');
                    cols.forEach(col => {
                        if (col.style.display !== 'none') {
                            if (contador % 4 === 0) {
                                if (currentRow) {
                                    container.appendChild(currentRow);
                                }
                                currentRow = document.createElement('div');
                                currentRow.classList.add('row');
                            }
                            currentRow.appendChild(col);
                            contador++;
                        }
                    });
                });

                if (currentRow) {
                    container.appendChild(currentRow);
                }

                container.scrollTop = scrollTop;
            }
        </script>
</body>

</html>