<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/conexao.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/global_constraints.php';

    header('Content-Type: application/json');
    $con = connect_local_mysqli('gestao_ambiental');

    $carregarDados = $_POST['carregarDados'] ?? '';

    if ($carregarDados == 'sim') {

        $sql = "SELECT fotos.*, setor, subsecao, local, ocorrencia FROM fotos
                LEFT JOIN setores ON setores.id = fotos.id_setor
                LEFT JOIN subsecoes ON subsecoes.id = fotos.id_subsecao
                LEFT JOIN local ON local.id = fotos.id_local
                LEFT JOIN ocorrencia ON ocorrencia.id = fotos.id_ocorrencia";
        $resultado = mysqli_query($con, $sql);

        $dados = [];

        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $dados[] = [
                    'id' => $row['id_fotos'],
                    'nome_arquivo' => $row['nome_arquivo'],
                    'data' => $row['data'],
                    'setor' => $row['setor'],
                    'subsecao' => $row['subsecao'],
                    'local' => $row['local'],
                    'ocorrencia' => $row['ocorrencia'],
                    'observacao' => $row['observacao'],
                    'conforme' => $row['conforme'],
                    'lcastanheira' => $row['lcastanheira'],
                    'lpaubrasil' => $row['lpaubrasil'],
                    'limbauba' => $row['limbauba']
                ];
            }

            if (empty($dados)) {
                echo json_encode(["error" => "Consulta não retornou dados."]);
            } else {
                echo json_encode(["dados" => $dados]);
            }
        } else {
            echo json_encode(["error" => "Erro na execução da consulta."]);
        }
        exit;
    }

    if ($carregarDados == 'nao') {

        $data = $_REQUEST['data'] ?? '';
        $id_setor = $_REQUEST['setor'] ?? '';
        $id_subsecao = $_REQUEST['subsecao'] ?? '';
        $id_local = $_REQUEST['local'] ?? '';
        $id_ocorrencia = $_REQUEST['ocorrencia'] ?? '';
        $observacao = $_REQUEST['observacao'] ?? '';
        $conforme = $_REQUEST['conforme'] ?? '';
        $castanheira = $_REQUEST['castanheira'] ?? '';
        $imbauba = $_REQUEST['imbauba'] ?? '';
        $paubrasil = $_REQUEST['paubrasil'] ?? '';
    
        $dataFormatada = date('dmY', strtotime($data));
        $timestamp = time();
        $nome_arquivo = "SGA_{$dataFormatada}_{$timestamp}.jpg";

        // Verifica se o arquivo foi enviado
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
            $temp_path = $_FILES['imagem']['tmp_name'];
            $destination_path = FOTOS_DIR . $nome_arquivo;

            // Move o arquivo para o novo diretório com o novo nome
            if (move_uploaded_file($temp_path, $destination_path)) {
                // Insere novo registro no banco de dados
                $sql = "INSERT INTO fotos (nome_arquivo, data, id_setor, id_subsecao, id_local, id_ocorrencia, observacao, conforme, lcastanheira, limbauba, lpaubrasil) 
                        VALUES ('$nome_arquivo', '$data', '$id_setor', '$id_subsecao', '$id_local', '$id_ocorrencia', '$observacao', '$conforme', '$castanheira', '$imbauba', '$paubrasil')";
                $resultado = mysqli_query($con, $sql);

                if ($resultado) {
                    echo json_encode(["status" => "true", "message" => "Foto adicionada com sucesso."]);
                    exit;
                } else {
                    echo json_encode(["status" => "false", "message" => "Erro ao adicionar foto no banco de dados."]);
                    exit;
                }
            } else {
                echo json_encode(["status" => "false", "message" => "Erro ao mover o arquivo para o diretório de armazenamento."]);
                exit;
            }
        } else {
            echo json_encode(["status" => "false", "message" => "Nenhum arquivo foi enviado ou ocorreu um erro no upload."]);
            exit;
        }
    }

    if ($carregarDados == 'linha') {

        $id = $_POST['id'];

        $sql = "SELECT fotos.*, setor, subsecao, local, ocorrencia FROM fotos
                LEFT JOIN setores ON setores.id = fotos.id_setor
                LEFT JOIN subsecoes ON subsecoes.id = fotos.id_subsecao
                LEFT JOIN local ON local.id = fotos.id_local
                LEFT JOIN ocorrencia ON ocorrencia.id = fotos.id_ocorrencia
                WHERE id_fotos = '$id' ";

        $resultado = mysqli_query($con, $sql);

        $dados = [];

        if ($resultado) {

            $row = mysqli_fetch_assoc($resultado);

            $dados[] = [
                'id' => $row['id_fotos'],
                'id_setor' => $row['id_setor'],
                'id_subsecao' => $row['id_subsecao'],
                'id_local' => $row['id_local'],
                'id_ocorrencia' => $row['id_ocorrencia'],
                'observacao' => $row['observacao'],
                'nome_arquivo' => $row['nome_arquivo'],
                'data' => $row['data'],
                'conforme' => $row['conforme'],
                'lcastanheira' => $row['lcastanheira'],
                'limbauba' => $row['limbauba'],
                'lpaubrasil' => $row['lpaubrasil']
            ];


            if (empty($dados)) {
                echo json_encode(["error" => "Consulta não retornou dados."]);
            } else {
                echo json_encode(["dados" => $dados]);
            }
        } else {
            echo json_encode(["error" => "Erro na execução da consulta."]);
        }
        exit;
    }

    if ($carregarDados == 'delete') {
        $id = $_POST['id'];

        $sql = "SELECT nome_arquivo FROM fotos WHERE id_fotos = '$id'";
        $resultado = mysqli_query($con, $sql);

        if ($resultado) {
            $row = mysqli_fetch_assoc($resultado);
            $arquivo = $row['nome_arquivo'];

            $sqlDelete = "DELETE FROM fotos WHERE id_fotos = '$id'";
            $resultadoDelete = mysqli_query($con, $sqlDelete);

            if ($resultadoDelete) {
                $caminhoArquivo = FOTOS_DIR . $arquivo;

                if (file_exists($caminhoArquivo)) {
                    unlink($caminhoArquivo);
                }

                echo json_encode(["dados" => "Foto deletada com sucesso."]);
            } else {
                echo json_encode(["error" => "Erro ao deletar foto no banco de dados."]);
            }
        } else {
            echo json_encode(["error" => "Erro ao buscar o arquivo."]);
        }
        exit;
    }
}


if (isset($_GET['foto'])) {
    define('FOTOS_DIR', $_SERVER['DOCUMENT_ROOT'] . '/armazenamento/');
    $foto = basename($_GET['foto']);
    $caminhoCompleto = FOTOS_DIR . $foto;

    if (file_exists($caminhoCompleto)) {
        header('Content-Type: image/jpeg');
        readfile($caminhoCompleto);
        exit;
    } else {
        http_response_code(404);
        echo "Imagem não encontrada.";
    }
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/autoload.php';
require_once HOME_DIR . 'componentes/navbar.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="/includes/estilo.css" rel="stylesheet">
    <link rel="icon" href="/includes/logo.ico">
    <title>Upload fotos</title>

</head>


<body>

    <div class="container">
        <div class="container-fluid">
            <h1>Listar fotos</h1>
        </div>

        <div class="container-fluid">
            <div class="row justify-content-end">
                <div class="col-md-3 d-flex justify-content-end">
                    <button type="button" class="btn btn-dark" id="addBtnFoto">Carregar Foto</button>
                </div>
            </div>
            <br>
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <table width="100%" id="tabela_fotos" class="table table-striped tabela">
                        <thead>
                            <tr>
                                <th>Conforme</th>
                                <th>Foto</th>
                                <th>Setor</th>
                                <th>Subseção</th>
                                <th>Local</th>
                                <th>Ocorrência</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dados serão preenchidos pelo DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal01" tabindex="-1" aria-labelledby="modal01" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Carregar Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" enctype="multipart/form-data">
                        <input type="hidden" class="form-control" id="id_foto" name="id_foto">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="data" class="form-label"><strong>Data:</strong></label>
                                <input type="date" class="form-control form-control-sm" id="data" name="data">
                            </div>
                            <div class="col-md-4">
                                <label for="setor" class="form-label"><strong>Setor:</strong></label>
                                <select class="form-select ml-2" id="setor" name="setor" required>
                                    <option value="" disabled selected>Selecione um setor...</option>
                                    <?php
                                    $con = connect_local_mysqli('gestao_ambiental');
                                    $sql = "SELECT * FROM setores";
                                    $resultado = mysqli_query($con, $sql);
                                    while ($row = mysqli_fetch_assoc($resultado)) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['setor'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="subsecao" class="form-label"><strong>Subseção:</strong></label>
                                <select class="form-select ml-2" id="subsecao" name="subsecao" required>
                                    <option value="" disabled selected>Selecione uma subseção...</option>
                                    <?php
                                    $con = connect_local_mysqli('gestao_ambiental');
                                    $sql = "SELECT * FROM subsecoes";
                                    $resultado = mysqli_query($con, $sql);
                                    while ($row = mysqli_fetch_assoc($resultado)) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['subsecao'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="local" class="form-label"><strong>Local:</strong></label>
                                <select class="form-select ml-2" id="local" name="local" required>
                                    <option value="" disabled selected>Selecione um local...</option>
                                    <?php
                                    $con = connect_local_mysqli('gestao_ambiental');
                                    $sql = "SELECT * FROM local";
                                    $resultado = mysqli_query($con, $sql);
                                    while ($row = mysqli_fetch_assoc($resultado)) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['local'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="ocorrencia" class="form-label"><strong>Evidência:</strong></label>
                                <select class="form-select ml-2" id="ocorrencia" name="ocorrencia" required>
                                    <option value="" disabled selected>Selecione uma evidência...</option>
                                    <?php
                                    $con = connect_local_mysqli('gestao_ambiental');
                                    $sql = "SELECT * FROM ocorrencia";
                                    $resultado = mysqli_query($con, $sql);
                                    while ($row = mysqli_fetch_assoc($resultado)) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['ocorrencia'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="conforme" class="form-label"><strong>Conforme:</strong></label>
                                <select class="form-select ml-2" id="conforme" name="conforme" required>
                                    <option value="" disabled selected>Selecione uma opção...</option>
                                    <option value="S">Sim</option>
                                    <option value="N">Não</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="imbauba" class="form-label"><strong>Lista Imbaúba:</strong></label>
                                <select class="form-select ml-2" id="imbauba" name="imbauba" required>
                                    <option value="" disabled selected>Selecione um item...</option>
                                    <?php
                                    $con = connect_local_mysqli('gestao_ambiental');
                                    $sql = "SELECT * FROM lista_imbauba ORDER BY 2 ASC";
                                    $resultado = mysqli_query($con, $sql);
                                    while ($row = mysqli_fetch_assoc($resultado)) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['item'] . " - " . $row['desc_item'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="castanheira" class="form-label"><strong>Lista Castanheira:</strong></label>
                                <select class="form-select ml-2" id="castanheira" name="castanheira" required>
                                    <option value="" disabled selected>Selecione um item...</option>
                                    <?php
                                    $con = connect_local_mysqli('gestao_ambiental');
                                    $sql = "SELECT * FROM lista_castanheira ORDER BY 2 ASC";
                                    $resultado = mysqli_query($con, $sql);
                                    while ($row = mysqli_fetch_assoc($resultado)) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['item'] . " - " . $row['desc_item'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="paubrasil" class="form-label"><strong>Lista Pau Brasil:</strong></label>
                                <select class="form-select ml-2" id="paubrasil" name="paubrasil" required>
                                    <option value="" disabled selected>Selecione um item...</option>
                                    <?php
                                    $con = connect_local_mysqli('gestao_ambiental');
                                    $sql = "SELECT * FROM lista_pau_brasil ORDER BY 2 ASC";
                                    $resultado = mysqli_query($con, $sql);
                                    while ($row = mysqli_fetch_assoc($resultado)) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['item'] . " - " . $row['desc_item'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="observacao" class="form-label"><strong>Ação Corretiva:</strong></label>
                            <textarea class="form-control" id="observacao" name="observacao" rows="3" max="200"></textarea>
                            <div id="charCount" class="text-end" style="font-size: 12px;">200 caracteres restantes</div>
                        </div>

                    </form>
                    <div class="mb-3">
                        <label for="imagem" class="form-label"><strong>Escolher arquivo</strong></label>
                        <input class="form-control" type="file" id="imagem" name="imagem">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-dark" id="saveBtn">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal02" tabindex="-1" aria-labelledby="modal01" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detalhes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="id_fotoV" name="id_fotoV">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="dataV" class="form-label"><strong>Data:</strong></label>
                            <input type="date" class="form-control form-control-sm" id="dataV" name="dataV" disabled>
                        </div>
                        <div class="col-md-4">
                            <label for="setorV" class="form-label"><strong>Setor:</strong></label>
                            <select class="form-select ml-2" id="setorV" name="setorV" required disabled>
                                <option value="" disabled selected>Selecione um setor...</option>
                                <?php
                                $con = connect_local_mysqli('gestao_ambiental');
                                $sql = "SELECT * FROM setores ORDER BY 2 ASC";
                                $resultado = mysqli_query($con, $sql);
                                while ($row = mysqli_fetch_assoc($resultado)) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['setor'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="subsecaoV" class="form-label"><strong>Subseção:</strong></label>
                            <select class="form-select ml-2" id="subsecaoV" name="subsecaoV" required disabled>
                                <option value="" disabled selected>Selecione uma subseção...</option>
                                <?php
                                $con = connect_local_mysqli('gestao_ambiental');
                                $sql = "SELECT * FROM subsecoes ORDER BY 2 ASC";
                                $resultado = mysqli_query($con, $sql);
                                while ($row = mysqli_fetch_assoc($resultado)) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['subsecao'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="localV" class="form-label"><strong>Local:</strong></label>
                            <select class="form-select ml-2" id="localV" name="localV" required disabled>
                                <option value="" disabled selected>Selecione um local...</option>
                                <?php
                                $con = connect_local_mysqli('gestao_ambiental');
                                $sql = "SELECT * FROM local ORDER BY 2 ASC";
                                $resultado = mysqli_query($con, $sql);
                                while ($row = mysqli_fetch_assoc($resultado)) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['local'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="ocorrenciaV" class="form-label"><strong>Evidência:</strong></label>
                            <select class="form-select ml-2" id="ocorrenciaV" name="ocorrenciaV" required disabled>
                                <option value="" disabled selected>Selecione uma evidência...</option>
                                <?php
                                $con = connect_local_mysqli('gestao_ambiental');
                                $sql = "SELECT * FROM ocorrencia";
                                $resultado = mysqli_query($con, $sql);
                                while ($row = mysqli_fetch_assoc($resultado)) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['ocorrencia'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="conformeV" class="form-label"><strong>Conforme:</strong></label>
                            <select class="form-select ml-2" id="conformeV" name="conformeV" required disabled>
                                <option value="" disabled selected>Selecione uma opção...</option>
                                <option value="S">Sim</option>
                                <option value="N">Não</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="imbaubaV" class="form-label"><strong>Lista Imbaúba:</strong></label>
                            <select class="form-select ml-2" id="imbaubaV" name="imbaubaV" required disabled>
                                <option value="" disabled selected>Selecione um item...</option>
                                <?php
                                $con = connect_local_mysqli('gestao_ambiental');
                                $sql = "SELECT * FROM lista_imbauba ORDER BY 2 ASC";
                                $resultado = mysqli_query($con, $sql);
                                while ($row = mysqli_fetch_assoc($resultado)) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['item'] . " - " . $row['desc_item'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="castanheiraV" class="form-label"><strong>Lista Castanheira:</strong></label>
                            <select class="form-select ml-2" id="castanheiraV" name="castanheiraV" required disabled>
                                <option value="" disabled selected>Selecione um item...</option>
                                <?php
                                $con = connect_local_mysqli('gestao_ambiental');
                                $sql = "SELECT * FROM lista_castanheira ORDER BY 2 ASC";
                                $resultado = mysqli_query($con, $sql);
                                while ($row = mysqli_fetch_assoc($resultado)) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['item'] . " - " . $row['desc_item'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="paubrasilV" class="form-label"><strong>Lista Pau Brasil:</strong></label>
                            <select class="form-select ml-2" id="paubrasilV" name="paubrasilV" required disabled>
                                <option value="" disabled selected>Selecione um item...</option>
                                <?php
                                $con = connect_local_mysqli('gestao_ambiental');
                                $sql = "SELECT * FROM lista_pau_brasil ORDER BY 2 ASC";
                                $resultado = mysqli_query($con, $sql);
                                while ($row = mysqli_fetch_assoc($resultado)) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['item'] . " - " . $row['desc_item'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="observacaoV" class="form-label"><strong>Ação Corretiva:</strong></label>
                        <textarea class="form-control" id="observacaoV" name="observacaoV" rows="3" max="200" disabled></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fotoModalLabel">Visualização da Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="fotoModalImg" src="" alt="Foto" class="img-fluid">
                </div>
            </div>
        </div>
    </div>



    <footer>Desenvolvido por: Douglas Marcondes.</footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.0.6/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.6/js/dataTables.bootstrap5.min.js"></script>

    <script type="text/javascript">
        let datatable;
        var INCLUDES_URL = "<?php echo INCLUDES_URL; ?>";

        $('#conforme').val('');
        $('#id_foto').val('');
        $('#data').val('');
        $('#setor').val('');
        $('#subsecao').val('');
        $('#local').val('');
        $('#ocorrencia').val('');
        $('#observacao').val('');
        $('#castanheira').val('');
        $('#imbauba').val('');
        $('#paubrasil').val('');
        
        const observacao = document.getElementById('observacao');
        const maxChars = 200;

        function enforceMaxLength(input) {
            input.addEventListener('input', function() {
                if (input.value.length > maxChars) {
                    alert("Você ultrapassou o limite de 200 caracteres.");
                    input.value = input.value.substring(0, maxChars);
                }

                const remainingChars = maxChars - input.value.length;
                const charCountElement = input.nextElementSibling;
                charCountElement.textContent = `${remainingChars} caracteres restantes`;
            });
        }

        enforceMaxLength(observacao);

        $(document).ready(function() {
            document.getElementById("addBtnFoto").addEventListener("click", function() {
                $('#modal01').modal('show');
            });

            carregarDados();

            $('#tabela_fotos').on('click', '.foto-link', function(event) {
                event.preventDefault();

                // Obtém o nome do arquivo a partir do atributo data-foto
                const nomeArquivo = $(this).data('foto');

                // Monta a URL para o PHP que exibirá a imagem
		const caminhoFoto = 'http://gestambi.com.br/armazenamento/' + encodeURIComponent(nomeArquivo);

                // Define o src da imagem na modal e exibe a modal
                $('#fotoModalImg').attr('src', caminhoFoto);
                $('#fotoModal').modal('show');
            });
        });

        async function carregarDatatable(data) {
            if (datatable) {
                datatable.clear().rows.add(data).draw();
            } else {
                datatable = $('#tabela_fotos').DataTable({
                    language: {
                        "url": "/includes/datatablesPortugues.json"
                    },
                    data: data,
                    columns: [{
                            "data": null,
                            "defaultContent": "-"
                        },
                        {
                            "data": "nome_arquivo",
                            render: function(data, type, row) {
                                if (type === 'display') {
                                    return '<a href="#" class="foto-link" data-foto="' + data + '">' + data + '</a>';
                                }
                                return data;
                            },
                            "defaultContent": "-"
                        },
                        {
                            "data": "setor",
                            "defaultContent": "-",
                        },
                        {
                            "data": "subsecao",
                            "defaultContent": "-",
                        },
                        {
                            "data": "local",
                            "defaultContent": "-",
                        },
                        {
                            "data": "ocorrencia",
                            "defaultContent": "-",
                        },
                        {
                            "data": "data",
                            "render": function(data) {
                                if (data) {
                                    const dateParts = data.split('-');
                                    return `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;
                                }
                                return '';
                            },
                            "defaultContent": "-",
                        },
                        {
                            "data": null,
                            "defaultContent": `
                                                <div class="d-flex align-items-center">
                                                    <button class="btn btn-sm btn-outline-dark edit me-1">
                                                        <i class="bi bi-laptop"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            `,
                            "width": "90px"

                        }
                    ],
                    columnDefs: [{
                            targets: '_all',
                            className: 'text-center'
                        }, {
                            targets: 0,
                            render: function(data, type, row) {

                                if (data.conforme == 'S') {
                                    return `<img src='${INCLUDES_URL}/alerta/ok.png' title='Conforme' style='width: 25px; height: 25px;' />`;
                                } else if (data.conforme == 'N') {
                                    return `<img src='${INCLUDES_URL}/alerta/erro.png' title='Não Conforme' style='width: 25px; height: 25px;' />`;
                                } else if (data.conforme == 'P') {
                                    return `<img src='${INCLUDES_URL}/alerta/parcial.png' title='Parcialmente Conforme' style='width: 25px; height: 25px;'/>`;
                                }

                                return ' ';

                            }
                        }

                    ],
                    ordering: true,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "Todos"]
                    ],
                    drawCallback: function(settings) {

                        var api = this.api();

                        $('#tabela_fotos tbody').on('click', '.edit', function(event) {

                            var data = $('#tabela_fotos').DataTable().row($(this).closest('tr')).data();
                            var id = data.id;

                            $('#modal02').modal('show');

                            $.ajax({
                                url: "registrar_fotos.php",
                                method: 'POST',
                                data: {
                                    id: id,
                                    carregarDados: "linha"
                                },
                                success: function(response) {

                                    if (response.dados && response.dados.length > 0) {
                                        var data = response.dados[0];

                                        $('#id_fotoV').val(data.id);
                                        $('#dataV').val(data.data);
                                        $('#setorV').val(data.id_setor);
                                        $('#subsecaoV').val(data.id_subsecao);
                                        $('#localV').val(data.id_local);
                                        $('#ocorrenciaV').val(data.id_ocorrencia);
                                        $('#observacaoV').val(data.observacao);
                                        $('#conformeV').val(data.conforme);
                                        $('#castanheiraV').val(data.lcastanheira);
                                        $('#imbaubaV').val(data.limbauba);
                                        $('#paubrasilV').val(data.lpaubrasil);

                                        $('#modal02').modal('show');
                                    } else {
                                        console.error('Nenhum dado encontrado.');
                                        alert('Erro: Nenhum dado encontrado.');
                                    }
                                },

                                error: function(xhr, status, error) {
                                    console.error('Erro no AJAX:', error);
                                }
                            });
                        });

                        $('#tabela_fotos tbody').on('click', '.delete', function(event) {
                            var data = $('#tabela_fotos').DataTable().row($(this).closest('tr')).data();
                            var id = data.id;

                            if (confirm('Você tem certeza que deseja deletar esta foto?')) {
                                $.ajax({
                                    url: "registrar_fotos.php",
                                    method: 'POST',
                                    data: {
                                        id: id,
                                        carregarDados: "delete"
                                    },
                                    success: function(response) {

                                        console.log(response);

                                        if (response.dados) {
                                            window.location.reload();
                                        } else {
                                            console.error('Erro na resposta:', response.error);
                                            alert('Erro: ' + response.error);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Erro no AJAX:', error);
                                        alert('Erro na requisição: ' + error);
                                    }
                                });
                            }
                        });
                    }
                });
            }
        }

        function carregarDados() {
            $.ajax({
                url: 'registrar_fotos.php',
                type: "POST",
                data: {
                    carregarDados: "sim"
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Resposta do servidor:', response);
                    if (response.dados && Array.isArray(response.dados)) {
                        carregarDatatable(response.dados);
                    } else if (response.error) {
                        console.error('Erro no servidor:', response.error);
                    } else {
                        console.error('Dados inválidos recebidos do servidor:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro na requisição:', error);
                }
            });
        }

        $('#saveBtn').on('click', function() {
            var formData = new FormData();

            var fileInput = $('#imagem')[0].files[0];
            var conforme = $('#conforme').val();
            var setor = $('#setor').val();
            var data = $('#data').val();
            var subsecao = $('#subsecao').val();
            var local = $('#local').val();
            var ocorrencia = $('#ocorrencia').val();
            var observacao = $('#observacao').val();
            var castanheira = $('#castanheira').val();
            var imbauba = $('#imbauba').val();
            var paubrasil = $('#paubrasil').val();

            formData.append('imagem', fileInput);
            formData.append('conforme', conforme);
            formData.append('setor', setor);
            formData.append('data', data);
            formData.append('subsecao', subsecao);
            formData.append('local', local);
            formData.append('ocorrencia', ocorrencia);
            formData.append('observacao', observacao);
            formData.append('castanheira', castanheira);
            formData.append('paubrasil', paubrasil);
            formData.append('imbauba', imbauba);
            formData.append('carregarDados', 'nao');

            $.ajax({
                url: 'registrar_fotos.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log("Resposta do servidor:", response);
                    $('#modal01').modal('hide');

                    if (response.status === 'true') {
                        window.location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erro na requisição:", error);
                    console.error("Resposta completa:", xhr.responseText);
                    alert('Erro na requisição: ' + error);
                }
            });
        });
    </script>
</body>

</html>