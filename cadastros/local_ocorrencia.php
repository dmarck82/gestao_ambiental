<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ativa a exibição de erros para depuração
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // Conexão com o banco de dados
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/conexao.php';
    header('Content-Type: application/json');
    $con = connect_local_mysqli('gestao_ambiental');

    if (mysqli_connect_errno()) {
        die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
    }

    // Função para proteger contra SQL Injection
    function escapeInput($input, $con) {
        return mysqli_real_escape_string($con, trim($input));
    }

    // Processamento para 'local'
    if ($_POST['qual'] == 'local') {

        $carregarDados = $_POST['carregarDados'] ?? '';

        // Carregar todos os dados de 'local'
        if ($carregarDados == 'sim') {
            $sql = "SELECT * FROM local";
            $resultado = mysqli_query($con, $sql);

            $dados = [];

            if ($resultado) {
                while ($row = mysqli_fetch_assoc($resultado)) {
                    $dados[] = [
                        'id' => $row['id'],
                        'local' => $row['local']
                    ];
                }

                if (empty($dados)) {
                    echo json_encode(["error" => "Consulta não retornou dados."]);
                } else {
                    echo json_encode(["dados" => $dados]);
                }
            } else {
                echo json_encode(["error" => "Erro na execução da consulta: " . mysqli_error($con)]);
            }
            exit;
        }

        // Atualizar ou inserir um novo 'local'
        if ($carregarDados == 'nao') {
            $id = $_REQUEST['id'] ?? '';
            $local = $_REQUEST['local'] ?? '';

            if (empty($local)) {
                echo json_encode(["error" => "Local não informado."]);
                exit;
            }

            // Protege contra SQL Injection
            $id = escapeInput($id, $con);
            $local = escapeInput($local, $con);

            if (!empty($id)) {
                $sql = "UPDATE local SET local = '$local' WHERE id = '$id'";
                $resultado = mysqli_query($con, $sql);

                if ($resultado) {
                    echo json_encode(["status" => "true", "message" => "Setor atualizado com sucesso."]);
                } else {
                    echo json_encode(["status" => "false", "message" => "Erro ao atualizar setor: " . mysqli_error($con)]);
                }
                exit;
            } else {
                $sql = "INSERT INTO local (local) VALUES ('$local')";
                $resultado = mysqli_query($con, $sql);

                if ($resultado) {
                    echo json_encode(["status" => "true", "message" => "Setor adicionado com sucesso."]);
                } else {
                    echo json_encode(["status" => "false", "message" => "Erro ao adicionar setor: " . mysqli_error($con)]);
                }
                exit;
            }
        }

        // Carregar dados de uma linha específica
        if ($carregarDados == 'linha') {
            $id = $_POST['id'] ?? '';

            if (empty($id)) {
                echo json_encode(["error" => "ID não fornecido."]);
                exit;
            }

            // Protege contra SQL Injection
            $id = escapeInput($id, $con);

            $sql = "SELECT * FROM local WHERE id = '$id' ";
            $resultado = mysqli_query($con, $sql);

            $dados = [];

            if ($resultado) {
                $row = mysqli_fetch_assoc($resultado);

                if ($row) {
                    $dados[] = [
                        'id' => $row['id'],
                        'local' => $row['local']
                    ];
                    echo json_encode(["dados" => $dados]);
                } else {
                    echo json_encode(["error" => "Consulta não retornou dados."]);
                }
            } else {
                echo json_encode(["error" => "Erro na execução da consulta: " . mysqli_error($con)]);
            }
            exit;
        }

        // Deletar 'local'
        if ($carregarDados == 'delete') {
            $id = $_POST['id'] ?? '';

            if (empty($id)) {
                echo json_encode(["error" => "ID não fornecido."]);
                exit;
            }

            // Protege contra SQL Injection
            $id = escapeInput($id, $con);

            $sql = "DELETE FROM local WHERE id = '$id' ";
            $resultado = mysqli_query($con, $sql);

            if ($resultado) {
                echo json_encode(["dados" => "Setor deletado com sucesso."]);
            } else {
                echo json_encode(["error" => "Erro na execução da consulta: " . mysqli_error($con)]);
            }
            exit;
        }
    }

    // Processamento para 'ocorrencia'
    if ($_POST['qual'] == 'ocorrencia') {

        $carregarDados = $_POST['carregarDados'] ?? '';

        // Carregar todos os dados de 'ocorrencia'
        if ($carregarDados == 'sim') {
            $sql = "SELECT * FROM ocorrencia";
            $resultado = mysqli_query($con, $sql);

            $dados = [];

            if ($resultado) {
                while ($row = mysqli_fetch_assoc($resultado)) {
                    $dados[] = [
                        'id' => $row['id'],
                        'ocorrencia' => $row['ocorrencia']
                    ];
                }

                if (empty($dados)) {
                    echo json_encode(["error" => "Consulta não retornou dados."]);
                } else {
                    echo json_encode(["dados" => $dados]);
                }
            } else {
                echo json_encode(["error" => "Erro na execução da consulta: " . mysqli_error($con)]);
            }
            exit;
        }

        // Atualizar ou inserir um novo 'ocorrencia'
        if ($carregarDados == 'nao') {
            $id = $_REQUEST['id'] ?? '';
            $ocorrencia = $_REQUEST['ocorrencia'] ?? '';

            if (empty($ocorrencia)) {
                echo json_encode(["error" => "Ocorrência não informada."]);
                exit;
            }

            // Protege contra SQL Injection
            $id = escapeInput($id, $con);
            $ocorrencia = escapeInput($ocorrencia, $con);

            if (!empty($id)) {
                $sql = "UPDATE ocorrencia SET ocorrencia = '$ocorrencia' WHERE id = '$id'";
                $resultado = mysqli_query($con, $sql);

                if ($resultado) {
                    echo json_encode(["status" => "true", "message" => "Ocorrência atualizada com sucesso."]);
                } else {
                    echo json_encode(["status" => "false", "message" => "Erro ao atualizar ocorrência: " . mysqli_error($con)]);
                }
                exit;
            } else {
                $sql = "INSERT INTO ocorrencia (ocorrencia) VALUES ('$ocorrencia')";
                $resultado = mysqli_query($con, $sql);

                if ($resultado) {
                    echo json_encode(["status" => "true", "message" => "Ocorrência adicionada com sucesso."]);
                } else {
                    echo json_encode(["status" => "false", "message" => "Erro ao adicionar ocorrência: " . mysqli_error($con)]);
                }
                exit;
            }
        }

        // Carregar dados de uma linha específica
        if ($carregarDados == 'linha') {
            $id = $_POST['id'] ?? '';

            if (empty($id)) {
                echo json_encode(["error" => "ID não fornecido."]);
                exit;
            }

            // Protege contra SQL Injection
            $id = escapeInput($id, $con);

            $sql = "SELECT * FROM ocorrencia WHERE id = '$id' ";
            $resultado = mysqli_query($con, $sql);

            $dados = [];

            if ($resultado) {
                $row = mysqli_fetch_assoc($resultado);

                if ($row) {
                    $dados[] = [
                        'id' => $row['id'],
                        'ocorrencia' => $row['ocorrencia']
                    ];
                    echo json_encode(["dados" => $dados]);
                } else {
                    echo json_encode(["error" => "Consulta não retornou dados."]);
                }
            } else {
                echo json_encode(["error" => "Erro na execução da consulta: " . mysqli_error($con)]);
            }
            exit;
        }

        // Deletar 'ocorrencia'
        if ($carregarDados == 'delete') {
            $id = $_POST['id'] ?? '';

            if (empty($id)) {
                echo json_encode(["error" => "ID não fornecido."]);
                exit;
            }

            // Protege contra SQL Injection
            $id = escapeInput($id, $con);

            $sql = "DELETE FROM ocorrencia WHERE id = '$id' ";
            $resultado = mysqli_query($con, $sql);

            if ($resultado) {
                echo json_encode(["dados" => "Ocorrência deletada com sucesso."]);
            } else {
                echo json_encode(["error" => "Erro na execução da consulta: " . mysqli_error($con)]);
            }
            exit;
        }
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
    <title>Local/Evidência</title>

</head>

<body>

    <div class="container">
        <div class="container-fluid">
            <div class="container">
                <h1>Local/Ocorrência</h1>
            </div>
            <div class="row">
                <!-- Tabela de Setores -->
                <div class="col-md-6 mt-3">
                    <div class="row justify-content-end mb-2">
                        <div class="col-auto">
                            <button type="button" class="btn btn-dark" id="addBtnLocal">Cadastrar Local</button>
                        </div>
                    </div>
                    <table width="100%" id="tabela_local" class="table table-striped tabela">
                        <thead>
                            <tr>
                                <th>Local</th>
                                <th>Opções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Seus dados do data table aqui -->
                        </tbody>
                    </table>
                </div>

                <!-- Tabela de Evidência -->
                <div class="col-md-6 mt-3">
                    <div class="row justify-content-end mb-2">
                        <div class="col-auto">
                            <button type="button" class="btn btn-dark" id="addBtnOcorrencia">Cadastrar Evidência</button>
                        </div>
                    </div>
                    <table width="100%" id="tabela_ocorrencia" class="table table-striped tabela">
                        <thead>
                            <tr>
                                <th>Evidência</th>
                                <th>Opções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Seus dados do data table aqui -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 01 -->
    <div class="modal fade" id="modal01" tabindex="-1" aria-labelledby="modal01" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cadastrar Local</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulário dentro do modal -->
                    <form>
                        <input type="hidden" class="form-control" id="id_local" name="id_local">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="local" class="form-label">Local:</label>
                                <input type="text" class="form-control" id="local" name="local" value="">
                                <span style="font-size: 12px;">30 caracteres restantes</span>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-dark" id="saveBtnLocal">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 02 -->
    <div class="modal fade" id="modal02" tabindex="-1" aria-labelledby="modal02" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cadastrar Evidência</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulário dentro do modal -->
                    <form>
                        <input type="hidden" class="form-control" id="id_ocorrencia" name="id_ocorrencia">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="ocorrencia" class="form-label">Evidência:</label>
                                <input type="text" class="form-control" id="ocorrencia" name="ocorrencia" value="">
                                <span style="font-size: 12px;">100 caracteres restantes</span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-dark" id="saveBtnOcorrencia">Salvar</button>
                </div>
            </div>
        </div>
    </div>



    <footer>Desenvolvido por: Douglas Marcondes.</footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.0.6/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.6/js/dataTables.bootstrap5.min.js"></script>

    <script type="text/javascript">
        let datatable_local;
        let datatable_ocorrencia;

        $('#id_local').val('');
        $('#local').val('');

        $('#id_ocorrencia').val('');
        $('#ocorrencia').val('');

        $(document).ready(function() {

            document.getElementById("addBtnLocal").addEventListener("click", function() {
                $('#modal01').modal('show');
            });
            document.getElementById("addBtnOcorrencia").addEventListener("click", function() {
                $('#modal02').modal('show');
            });

            carregarDadosLocal();
            carregarDadosOcorrencia();
        });

        const local = document.getElementById('local');
        const maxChars = 30;
        
        function enforceMaxLength(input) {
            input.addEventListener('input', function() {
                if (input.value.length > maxChars) {
                    alert("Você ultrapassou o limite de 100 caracteres.");
                    input.value = input.value.substring(0, maxChars);
                }
                
                const remainingChars = maxChars - input.value.length;
                const charCountElement = input.nextElementSibling;
                charCountElement.textContent = `${remainingChars} caracteres restantes`;
            });
        }
        
        const ocorrencia = document.getElementById('ocorrencia');
        const maxChars1 = 100;
        function enforceMaxLength1(input) {
            input.addEventListener('input', function() {
                if (input.value.length > maxChars1) {
                    alert("Você ultrapassou o limite de 100 caracteres.");
                    input.value = input.value.substring(0, maxChars1);
                }

                const remainingChars = maxChars1 - input.value.length;
                const charCountElement = input.nextElementSibling;
                charCountElement.textContent = `${remainingChars} caracteres restantes`;
            });
        }

        enforceMaxLength(local);
        enforceMaxLength1(ocorrencia);


        /*----------------FUNÇÕES PARA O CRUD LOCAL ----------------------------------*/

        async function carregarDatatableLocal(data) {
            if (datatable_local) {
                datatable.clear().rows.add(data).draw();
            } else {
                datatable_local = $('#tabela_local').DataTable({
                    language: {
                        "url": "/includes/datatablesPortugues.json"
                    },
                    data: data,
                    columns: [{
                            "data": "local"
                        },
                        {
                            "data": null,
                            "defaultContent": `
                                                <div class="d-flex align-items-center">
                                                    <button class="btn btn-sm btn-outline-dark edit me-1">
                                                        <i class="bi bi-pencil"></i>
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
                    }],
                    ordering: true,
                    lengthChange: false,
                    paging: false,
                    info: false,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "Todos"]
                    ],
                    drawCallback: function(settings) {

                        var api = this.api();

                        $('#tabela_local tbody').on('click', '.edit', function(event) {

                            var data = $('#tabela_local').DataTable().row($(this).closest('tr')).data();
                            var id = data.id;

                            $('#modal01').modal('show');

                            $.ajax({
                                url: "local_ocorrencia.php",
                                method: 'POST',
                                data: {
                                    id: id,
                                    carregarDados: "linha",
                                    qual: "local"
                                },
                                success: function(response) {

                                    if (response.dados && response.dados.length > 0) {
                                        var data = response.dados[0];

                                        $('#id_local').val(data.id);
                                        $('#local').val(data.local);

                                        $('#modal01').modal('show');
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

                        $('#tabela_local tbody').on('click', '.delete', function(event) {
                            var data = $('#tabela_local').DataTable().row($(this).closest('tr')).data();
                            var id = data.id;

                            if (confirm('Você tem certeza que deseja deletar este local?')) {
                                $.ajax({
                                    url: "local_ocorrencia.php",
                                    method: 'POST',
                                    data: {
                                        id: id,
                                        carregarDados: "delete",
                                        qual: "local"
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

        function carregarDadosLocal() {
            $.ajax({
                url: 'local_ocorrencia.php',
                type: "POST",
                data: {
                    carregarDados: "sim",
                    qual: "local"
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Resposta do servidor:', response);
                    if (response.dados && Array.isArray(response.dados)) {
                        carregarDatatableLocal(response.dados);
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

        $('#modal01').on('click', '#saveBtnLocal', function() {
            var id = $('#id_local').val();
            var local = $('#local').val();

            $.ajax({
                url: 'local_ocorrencia.php',
                type: 'POST',
                data: {
                    id: id,
                    local: local,
                    carregarDados: 'nao',
                    qual: "local"
                },
                success: function(response) {
                    console.log(response);
                    $('#modal01').modal('hide');

                    if (response.status === 'true') {
                        window.location.reload();
                    } else {
                        alert(response.message);
                    }

                    $('#id').val('');
                    $('#local').val('');
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('Erro na requisição: ' + error);
                }
            });
        });

        /*----------------FUNÇÕES PARA O CRUD OCORRENCIA-----------------------------*/

        async function carregarDatatableOcorrencia(data) {
            if (datatable_ocorrencia) {
                datatable.clear().rows.add(data).draw();
            } else {
                datatable_ocorrencia = $('#tabela_ocorrencia').DataTable({
                    language: {
                        "url": "/includes/datatablesPortugues.json"
                    },
                    data: data,
                    columns: [{
                            "data": "ocorrencia"
                        },
                        {
                            "data": null,
                            "defaultContent": `
                                                <div class="d-flex align-items-center">
                                                    <button class="btn btn-sm btn-outline-dark edit1 me-1">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger delete1">
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
                    }],
                    lengthChange: false,
                    paging: false,
                    ordering: true,
                    info: false,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "Todos"]
                    ],
                    drawCallback: function(settings) {

                        var api = this.api();

                        $('#tabela_ocorrencia tbody').on('click', '.edit1', function(event) {

                            var data = $('#tabela_ocorrencia').DataTable().row($(this).closest('tr')).data();
                            var id = data.id;

                            $('#modal02').modal('show');

                            $.ajax({
                                url: "local_ocorrencia.php",
                                method: 'POST',
                                data: {
                                    id: id,
                                    carregarDados: "linha",
                                    qual: "ocorrencia"
                                },
                                success: function(response) {
                                    if (response.dados && response.dados.length > 0) {
                                        var data = response.dados[0];

                                        $('#id_ocorrencia').val(data.id);
                                        $('#ocorrencia').val(data.ocorrencia);

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

                        $('#tabela_ocorrencia tbody').on('click', '.delete1', function(event) {
                            var data = $('#tabela_ocorrencia').DataTable().row($(this).closest('tr')).data();
                            var id = data.id;

                            if (confirm('Você tem certeza que deseja deletar esta ocorrencia?')) {
                                $.ajax({
                                    url: "local_ocorrencia.php",
                                    method: 'POST',
                                    data: {
                                        id: id,
                                        carregarDados: "delete",
                                        qual: "ocorrencia"
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

        function carregarDadosOcorrencia() {
            $.ajax({
                url: 'local_ocorrencia.php',
                type: "POST",
                data: {
                    carregarDados: "sim",
                    qual: "ocorrencia"
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Resposta do servidor:', response);
                    if (response.dados && Array.isArray(response.dados)) {
                        carregarDatatableOcorrencia(response.dados);
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

        $('#modal02').on('click', '#saveBtnOcorrencia', function() {
            var id = $('#id_ocorrencia').val();
            var ocorrencia = $('#ocorrencia').val();

            $.ajax({
                url: 'local_ocorrencia.php',
                type: 'POST',
                data: {
                    id: id,
                    ocorrencia: ocorrencia,
                    carregarDados: 'nao',
                    qual: "ocorrencia"
                },
                success: function(response) {
                    console.log(response);
                    $('#modal02').modal('hide');

                    if (response.status === 'true') {
                        window.location.reload();
                    } else {
                        alert(response.message);
                    }

                    $('#id').val('');
                    $('#ocorrencia').val('');
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('Erro na requisição: ' + error);
                }
            });
        });
    </script>
</body>

</html>