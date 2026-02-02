<?php

use App\Http\Controllers\AgendaApunteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvancePlantillaController;
use App\Http\Controllers\BilleteraTransaccionController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CausaController;
use App\Http\Controllers\CausaPostaController;
use App\Http\Controllers\ClaseTribunalController;
use App\Http\Controllers\CompraPaqueteController;
use App\Http\Controllers\ConfirmacionController;
use App\Http\Controllers\CuerpoExpedienteController;
use App\Http\Controllers\DepositoController;
use App\Http\Controllers\DevolucionSaldoController;
use App\Http\Controllers\DistritoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\DocumentosCategoriaController;
use App\Http\Controllers\FinalCostoController;
use App\Http\Controllers\GestionAlternativaController;
use App\Http\Controllers\InformePostaController;
use App\Http\Controllers\JuzgadoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\MatrizCotizacionController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\PaqueteCausaController;
use App\Http\Controllers\PaqueteController;
use App\Http\Controllers\ParticipanteController;
use App\Http\Controllers\PerfilUsuarioController;
use App\Http\Controllers\PisoController;
use App\Http\Controllers\PostaController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\ProcuraduriaDescargaController;
use App\Http\Controllers\TablaConfigController;
use App\Http\Controllers\TipoLegalController;
use App\Http\Controllers\TipoPostaController;
use App\Http\Controllers\TribunalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BilleteraController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\ParametroVigenciaController;
use App\Http\Controllers\ProcuradorPagoController;
use App\Http\Controllers\RegistroLlamadaController;
use App\Http\Controllers\RetiroController;
use App\Http\Controllers\TransaccionesAdminController;
use App\Http\Controllers\TransaccionesCausaController;
use App\Http\Controllers\TransaccionesContadorController;
use App\Http\Controllers\VerificationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('registro', [AuthController::class, 'crearUsuario']);
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('auth/logout', [AuthController::class, 'logout']);

        // Usuarios
        Route::prefix('usuarios')->group(function () {
            Route::get('abogados', [UserController::class, 'listarAbogados']);
            Route::get('procuradores', [UserController::class, 'listarProcuradores']);
            Route::get('abogados-dependientes/{abogadoLiderId}', [UserController::class, 'listarAbogadosDependientes']);
            Route::get('abogados-dependientes/{abogadoLiderId}/paginado', [UserController::class, 'obtenerUsuariosDependientes']);
            Route::get('listar/{user?}', [UserController::class, 'show']);
            Route::get('obtener-uno/{usuarioId}', [UserController::class, 'obtenerUnUsuario']);


            Route::post('crear', [UserController::class, 'crearUsuario']);
            Route::patch('actualizar/{user}', [UserController::class, 'actualizarUsuario']);
            Route::delete('eliminar/{user}', [UserController::class, 'eliminarUsuario']);
        });

        // Perfil del usuario autenticado
        Route::prefix('perfil')->group(function () {
            Route::get('/', [PerfilUsuarioController::class, 'obtenerPerfil']);
            Route::post('actualizar', [PerfilUsuarioController::class, 'actualizarPerfil']);
            Route::post('cambiar-foto', [PerfilUsuarioController::class, 'actualizarFotoPerfil']);
            Route::patch('cambiar-password', [PerfilUsuarioController::class, 'cambiarPassword']);
        });
    });

    // Rutas para la verificación de códigos a correo
    Route::post('send-verification-code', [VerificationController::class, 'sendVerificationCode']);
    Route::post('verify-code', [VerificationController::class, 'verifyCode']);

    //Rutas sin autenticacion
    Route::get('tabla-config/datos', [TablaConfigController::class, 'show']);
    Route::get('tabla-config/aranceles-doc', [TablaConfigController::class, 'obtenerArancelAbogados']);
    Route::get('paquetes/listado', [PaqueteController::class, 'listadoPaquetes']);
    //Documentos y categorias
    Route::get('documentos-categorias/tramites/listado', [DocumentosCategoriaController::class, 'listarCategoriasTramites']);
    Route::get('documentos-categorias/normas/listado', [DocumentosCategoriaController::class, 'listarCategoriasNormas']);
    Route::get('documentos-categorias/subcategoria/listado/{documentosCategoria}', [DocumentosCategoriaController::class, 'listarSubcategorias']);
    Route::get('documentos/listado/normas/categoria/{categoria}', [DocumentoController::class, 'listarDocNormasActivas']);
    Route::get('documentos/listado/tramites/categoria/{categoria}', [DocumentoController::class, 'listarDocTramitesActivas']);
    //Videos
    Route::get('videos/listado', [VideoController::class, 'listarActivos']);

    Route::middleware(['auth:sanctum'])->group(function () {
        //Materia
        Route::get('materias', [MateriaController::class, 'index']);
        Route::get('materias/listar/{materia?}', [MateriaController::class, 'show']);
        Route::post('materias', [MateriaController::class, 'store']);
        Route::patch('materias/{materia}', [MateriaController::class, 'update']);
        Route::patch('materias/eliminar/{materia}', [MateriaController::class, 'destroy']);
        //TipoLegal
        Route::get('tipo-legal', [TipoLegalController::class, 'index']);
        Route::post('tipo-legal', [TipoLegalController::class, 'store']);
        Route::get('tipo-legal/listar/{tipoLegal?}', [TipoLegalController::class, 'show']);
        Route::patch('tipo-legal/{tipoLegal}', [TipoLegalController::class, 'update']);
        Route::patch('tipo-legal/eliminar/{tipoLegal}', [TipoLegalController::class, 'destroy']);
        Route::get('tipo-legal/materia/{materiaId}', [TipoLegalController::class, 'listarPorMateriaId']);
        Route::get('tipo-legal/listado-con-materia', [TipoLegalController::class, 'listarActivosConMateria']);
        //Categoria
        Route::get('categorias', [CategoriaController::class, 'index']);
        Route::post('categorias', [CategoriaController::class, 'store']);
        Route::get('categorias/listar/{categoria?}', [CategoriaController::class, 'show']);
        Route::patch('categorias/{categoria}', [CategoriaController::class, 'update']);
        Route::patch('categorias/eliminar/{categoria}', [CategoriaController::class, 'destroy']);
        //Piso
        Route::get('pisos', [PisoController::class, 'index']);
        Route::post('pisos', [PisoController::class, 'store']);
        Route::get('pisos/listar/{piso?}', [PisoController::class, 'show']);
        Route::patch('pisos/{piso}', [PisoController::class, 'update']);
        Route::patch('pisos/eliminar/{piso}', [PisoController::class, 'destroy']);
        //Distrito
        Route::get('distritos', [DistritoController::class, 'index']);
        Route::post('distritos', [DistritoController::class, 'store']);
        Route::get('distritos/listar/{distrito?}', [DistritoController::class, 'show']);
        Route::patch('distritos/{distrito}', [DistritoController::class, 'update']);
        Route::patch('distritos/eliminar/{distrito}', [DistritoController::class, 'destroy']);
        //Juzgado
        Route::get('juzgados', [JuzgadoController::class, 'index']);
        Route::post('juzgados', [JuzgadoController::class, 'store']);
        Route::get('juzgados/listar/{juzgado?}', [JuzgadoController::class, 'show']);
        Route::post('juzgados/{juzgado}', [JuzgadoController::class, 'update']); //Actualiza
        Route::patch('juzgados/eliminar/{juzgado}', [JuzgadoController::class, 'destroy']);
        //Clase Tribunal
        Route::get('clase-tribunal', [ClaseTribunalController::class, 'index']);
        Route::post('clase-tribunal', [ClaseTribunalController::class, 'store']);
        Route::get('clase-tribunal/{claseTribunal}', [ClaseTribunalController::class, 'show']);
        Route::get('clase-tribunal/activos/listar', [ClaseTribunalController::class, 'listarActivos']);
        Route::patch('clase-tribunal/{claseTribunal}', [ClaseTribunalController::class, 'update']);
        Route::patch('clase-tribunal/eliminar/{claseTribunal}', [ClaseTribunalController::class, 'destroy']);
        //Causa
        Route::get('causas', [CausaController::class, 'index']);
        Route::get('causas/lista/administrar', [CausaController::class, 'indexCausasAdmin']);
        Route::get('causas/listado/terminadas', [CausaController::class, 'indexTerminadas']);
        Route::get('causas/por-tipo-legal/{codigoLegal}', [CausaController::class, 'listadoPorCodigoLegal']);
        Route::get('causas/por-abogado/{abogadoId}', [CausaController::class, 'listadoPorAbogado']);
        Route::get('causas/por-categoria/{categoriaId}', [CausaController::class, 'listadoPorCategoria']);
        Route::get('causas/por-procurador/{procuradorId}', [CausaController::class, 'listadoPorProcurador']);
        Route::post('causas', [CausaController::class, 'store']);
        Route::get('causas/{causa}', [CausaController::class, 'show']);
        Route::patch('causas/{causa}', [CausaController::class, 'update']);
        Route::patch('causas/eliminar/{causa}', [CausaController::class, 'destroy']);
        Route::get('causas/listado/sin-paquete', [CausaController::class, 'listarCausasParaPaquete']);
        //Rutas de listado para seguimiento de ordenes
        Route::get('causas/listado/orden-giradas', [CausaController::class, 'listadoCausasOrdenGiradas']);
        Route::get('causas/listado/orden-pre-presupuestadas', [CausaController::class, 'listadoCausasOrdenPrePresupuestadas']);
        Route::get('causas/listado/orden-presupuestadas', [CausaController::class, 'listadoCausasOrdenPresupuestadas']);
        Route::get('causas/listado/orden-aceptadas', [CausaController::class, 'listadoCausasOrdenAceptadas']);
        Route::get('causas/listado/orden-dinero-entregado', [CausaController::class, 'listadoCausasOrdenDineroEntregado']);
        Route::get('causas/listado/orden-lista-realizar', [CausaController::class, 'listadoCausasOrdenListaRealizar']);
        Route::get('causas/listado/orden-descargadas', [CausaController::class, 'listadoCausasOrdenDescargadas']);
        Route::get('causas/listado/orden-pronuncio-abogado', [CausaController::class, 'listadoCausasOrdenPronuncioAbogado']);
        Route::get('causas/listado/orden-cuenta-conciliadas', [CausaController::class, 'listadoCausasOrdenCuentasConciliadas']);
        Route::get('causas/listado/orden-vencidas-leves', [CausaController::class, 'listadoCausasOrdenVencidasLeves']);
        Route::get('causas/listado/orden-vencidas-graves', [CausaController::class, 'listadoCausasOrdenVencidasGraves']);
        Route::get('causas/listado/con-billeteras/paginado', [CausaController::class, 'causasConBilletera']);
        Route::get('causas/listado/para-transaccion/origen', [CausaController::class, 'listarCausasConBilletera']);
        Route::get('causas/listado/para-transaccion/destino', [CausaController::class, 'listarCausasDestinoTransaccion']);
        Route::get('causas/listado/billeteras/usuario', [CausaController::class, 'listaCausasConBilleteraUsuario']);
        //Listado de seguimiento de causas de ordenes de lider
        Route::get('causas/listado/orden-giradas/lider', [CausaController::class, 'listadoCausasOrdenGiradasDeLider']);
        Route::get('causas/listado/orden-pre-presupuestadas/lider', [CausaController::class, 'listadoCausasOrdenPrePresupuestadasDeLider']);
        Route::get('causas/listado/orden-presupuestadas/lider', [CausaController::class, 'listadoCausasOrdenPresupuestadasDeLider']);
        Route::get('causas/listado/orden-aceptadas/lider', [CausaController::class, 'listadoCausasOrdenAceptadasDeLider']);
        Route::get('causas/listado/orden-dinero-entregado/lider', [CausaController::class, 'listadoCausasOrdenDineroEntregadoDeLider']);
        Route::get('causas/listado/orden-lista-realizar/lider', [CausaController::class, 'listadoCausasOrdenListaRealizarDeLider']);
        Route::get('causas/listado/orden-descargadas/lider', [CausaController::class, 'listadoCausasOrdenDescargadasDeLider']);
        Route::get('causas/listado/orden-pronuncio-abogado/lider', [CausaController::class, 'listadoCausasOrdenPronuncioAbogadoDeLider']);
        Route::get('causas/listado/orden-cuenta-conciliadas/lider', [CausaController::class, 'listadoCausasOrdenCuentasConciliadasDeLider']);
        Route::get('causas/listado/orden-vencidas-leves/lider', [CausaController::class, 'listadoCausasOrdenVencidasLevesDeLider']);
        Route::get('causas/listado/orden-vencidas-graves/lider', [CausaController::class, 'listadoCausasOrdenVencidasGravesDeLider']);
        Route::get('causas/dinero-comprometido/{causaId}', [CausaController::class, 'dineroComprometidoCausa']);
        Route::get('causas/general-comprometido/saldos', [CausaController::class, 'dineroComprometidoGeneral']);
        Route::get('causas/listado/costos-operativos', [CausaController::class, 'indexTodas']);
        Route::get('causas/listado/saldos-activos', [CausaController::class, 'listadoCausasActivasConBilleteras']);
        Route::get('causas/listado/saldos-terminados', [CausaController::class, 'listadoCausasTerminadasConBilleteras']);
        Route::get('causas/datos-tribunal/{causaId}', [CausaController::class, 'obtenerNombreTribunalDominante']);
        //Tribunal
        Route::get('tribunal', [TribunalController::class, 'index']);
        Route::post('tribunal', [TribunalController::class, 'store']);
        Route::get('tribunal/{tribunal}', [TribunalController::class, 'show']);
        Route::get('tribunal/causa/listar/{causaId}', [TribunalController::class, 'listarActivosPorCausa']);
        Route::patch('tribunal/{tribunal}', [TribunalController::class, 'update']);
        Route::patch('tribunal/eliminar/{tribunal}', [TribunalController::class, 'destroy']);
        Route::get('tribunal/causa/{causaId}', [TribunalController::class, 'listarPorCausaId']);
        //Cuerpo Expediente
        Route::get('cuerpo-expedientes', [CuerpoExpedienteController::class, 'index']);
        Route::get('cuerpo-expedientes/tribunal/listar/{tribunalId}', [CuerpoExpedienteController::class, 'listadoPorTribunal']);
        Route::get('cuerpo-expedientes/por-tribunal/{tribunalId}', [CuerpoExpedienteController::class, 'listarExpedientesDigitalDeTribunal']);
        Route::post('cuerpo-expedientes', [CuerpoExpedienteController::class, 'store']);
        Route::get('cuerpo-expedientes/{cuerpoExpediente}', [CuerpoExpedienteController::class, 'show']);
        Route::post('cuerpo-expedientes/{cuerpoExpediente}', [CuerpoExpedienteController::class, 'update']);
        Route::patch('cuerpo-expedientes/eliminar/{cuerpoExpediente}', [CuerpoExpedienteController::class, 'destroy']);
        //Participante
        Route::get('participantes', [ParticipanteController::class, 'index']);
        Route::get('participantes/causa/listar/{causaId}', [ParticipanteController::class, 'listadoPorCausa']);
        Route::post('participantes', [ParticipanteController::class, 'store']);
        Route::get('participantes/{participante}', [ParticipanteController::class, 'show']);
        Route::patch('participantes/{participante}', [ParticipanteController::class, 'update']);
        Route::patch('participantes/eliminar/{participante}', [ParticipanteController::class, 'destroy']);
        Route::get('participantes/causa/{causaId}', [ParticipanteController::class, 'listarPorCausaId']);
        //Depositos
        Route::get('depositos', [DepositoController::class, 'index']);
        Route::post('depositos', [DepositoController::class, 'store']);
        Route::get('depositos/{deposito}', [DepositoController::class, 'show']);
        Route::patch('depositos/{deposito}', [DepositoController::class, 'update']);
        Route::patch('depositos/eliminar/{deposito}', [DepositoController::class, 'destroy']);
        //Devolucion Saldo
        Route::get('devolucion-saldo', [DevolucionSaldoController::class, 'index']);
        Route::post('devolucion-saldo', [DevolucionSaldoController::class, 'store']);
        Route::get('devolucion-saldo/{devolucionSaldo}', [DevolucionSaldoController::class, 'show']);
        Route::patch('devolucion-saldo/{devolucionSaldo}', [DevolucionSaldoController::class, 'update']);
        Route::patch('devolucion-saldo/eliminar/{devolucionSaldo}', [DevolucionSaldoController::class, 'destroy']);
        //Avance plantilla
        Route::get('avance-plantillas', [AvancePlantillaController::class, 'index']);
        Route::post('avance-plantillas', [AvancePlantillaController::class, 'store']);
        Route::get('avance-plantillas/listar/{avancePlantilla?}', [AvancePlantillaController::class, 'show']);
        Route::get('avance-plantillas/listarPorId/{idPlantilla}', [AvancePlantillaController::class, 'listarPlantillaPorId']);
        Route::patch('avance-plantillas/{avancePlantilla}', [AvancePlantillaController::class, 'update']);
        Route::patch('avance-plantillas/eliminar/{avancePlantilla}', [AvancePlantillaController::class, 'destroy']);
        //Posta
        Route::get('postas', [PostaController::class, 'index']);
        Route::post('postas', [PostaController::class, 'store']);
        Route::get('postas/listar/{posta?}', [PostaController::class, 'show']);
        Route::get('postas/listarPorId/{idPlantilla?}', [PostaController::class, 'listarPorIdPlantilla']);
        Route::patch('postas/{posta}', [PostaController::class, 'update']);
        Route::patch('postas/eliminar/{posta}', [PostaController::class, 'destroy']);
        //Agenda apunte
        Route::get('agenda-apuntes', [AgendaApunteController::class, 'index']);
        Route::post('agenda-apuntes', [AgendaApunteController::class, 'store']);
        Route::get('agenda-apuntes/{agendaApunte}', [AgendaApunteController::class, 'show']);
        Route::patch('agenda-apuntes/{agendaApunte}', [AgendaApunteController::class, 'update']);
        Route::patch('agenda-apuntes/eliminar/{agendaApunte}', [AgendaApunteController::class, 'destroy']);
        //Causa Posta
        Route::get('causa-postas', [CausaPostaController::class, 'index']);
        Route::post('causa-postas', [CausaPostaController::class, 'store']);
        Route::get('causa-postas/{causaPosta}', [CausaPostaController::class, 'show']);
        Route::patch('causa-postas/{causaPosta}', [CausaPostaController::class, 'update']);
        Route::patch('causa-postas/eliminar/{causaPosta}', [CausaPostaController::class, 'destroy']);
        Route::get('causa-postas/litado/causa/{causaId}', [CausaPostaController::class, 'listadoActivosPorCausa']);
        Route::patch('causa-postas/eliminar-todo/{causaId}', [CausaPostaController::class, 'eliminarTodoPorCausa']);
        //Tipo posta
        Route::get('tipo-postas', [TipoPostaController::class, 'index']);
        Route::get('tipo-postas/listado', [TipoPostaController::class, 'listarActivos']);
        Route::post('tipo-postas', [TipoPostaController::class, 'store']);
        Route::get('tipo-postas/{tipoPosta}', [TipoPostaController::class, 'show']);
        Route::patch('tipo-postas/{tipoPosta}', [TipoPostaController::class, 'update']);
        Route::patch('tipo-postas/eliminar/{tipoPosta}', [TipoPostaController::class, 'destroy']);
        //Informe Posta
        Route::get('informe-postas', [InformePostaController::class, 'index']);
        Route::post('informe-postas', [InformePostaController::class, 'store']);
        Route::get('informe-postas/{informePosta}', [InformePostaController::class, 'show']);
        Route::patch('informe-postas/{informePosta}', [InformePostaController::class, 'update']);
        Route::patch('informe-postas/delete-truncamiento/{informePosta}', [InformePostaController::class, 'deleteTruncamiento']);
        Route::patch('informe-postas/eliminar/{informePosta}', [InformePostaController::class, 'destroy']);
        //Matriz cotizacion
        Route::get('matriz-cotizacion', [MatrizCotizacionController::class, 'index']);
        Route::get('matriz-cotizacion/{matrizCotizacion}', [MatrizCotizacionController::class, 'show']);
        Route::get('matriz-cotizacion/prioridad-condicion/{prioridad}/{condicion}', [MatrizCotizacionController::class, 'obtenerIdDePrioridadYCondicion']);
        Route::patch('matriz-cotizacion/{matrizCotizacion}', [MatrizCotizacionController::class, 'update']);
        //Orden
        Route::get('orden', [OrdenController::class, 'index']);
        Route::get('orden/listar-por-causa/{id?}', [OrdenController::class, 'listarPorCausa']);
        Route::get('orden/listar-por-causa/{idCausa}/procurador/{procuradorId}', [OrdenController::class, 'listarPorCausaDeProcurador']);
        Route::post('orden', [OrdenController::class, 'store']);
        Route::get('orden/listado/{orden?}', [OrdenController::class, 'show']);
        Route::patch('orden/{orden}', [OrdenController::class, 'update']);
        Route::patch('orden/eliminar/{orden}', [OrdenController::class, 'destroy']);
        Route::patch('orden/aceptar/{orden}', [OrdenController::class, 'aceptarOrden']);
        Route::patch('orden/sugerir-presupuesto/{orden}', [OrdenController::class, 'sugerirPresupuesto']);
        Route::get('orden/listado/entregar-presupuesto/procurador/{procuradorId}', [OrdenController::class, 'ordenesParaEntregarPresupuesto']);
        Route::get('orden/listado/devolver-presupuesto/procurador/{procuradorId}', [OrdenController::class, 'ordenesParaDevolverPresupuesto']);
        Route::get('orden/listado/sin-costojudicial-venta/admin', [OrdenController::class, 'ordenesParaColocarCostoJudicialVenta']);
        //Contador de seguimiento 
        Route::get('orden/cantidad-orden-etapas', [OrdenController::class, 'cantidadOrdenesEnEtapas']);
        //Contador de seguimiento de ordenes del Lider
        Route::get('orden/cantidad-orden-etapas/lider', [OrdenController::class, 'cantidadOrdenesEnEtapasDeLider']);
        //Seguimiento
        Route::get('orden/giradas/causa/{idCausa}', [OrdenController::class, 'listadoOrdenGiradas']);
        Route::get('orden/pre-presupuestadas/causa/{idCausa}', [OrdenController::class, 'listadoOrdenPrePresupuestadas']);
        Route::get('orden/presupuestadas/causa/{idCausa}', [OrdenController::class, 'listadoOrdenPresupuestadas']);
        Route::get('orden/aceptadas/causa/{idCausa}', [OrdenController::class, 'listadoOrdenAceptadas']);
        Route::get('orden/dinero-entregado/causa/{idCausa}', [OrdenController::class, 'listadoOrdenDineroEntregado']);
        Route::get('orden/lista-realizar/causa/{idCausa}', [OrdenController::class, 'listadoOrdenListaRealizar']);
        Route::get('orden/descargadas/causa/{idCausa}', [OrdenController::class, 'listadoOrdenDescargadas']);
        Route::get('orden/pronuncio-abogado/causa/{idCausa}', [OrdenController::class, 'listadoOrdenPronuncioAbogado']);
        Route::get('orden/cuenta-conciliada/causa/{idCausa}', [OrdenController::class, 'listadoOrdenCuentaConciliadas']);
        Route::get('orden/vencidas-leves/causa/{idCausa}', [OrdenController::class, 'listadoOrdenVencidasLeves']);
        Route::get('orden/vencidas-graves/causa/{idCausa}', [OrdenController::class, 'listadoOrdenVencidasGraves']);
        //Seguimiento de ordenes de Lider
        Route::get('orden/giradas-lider/causa/{idCausa}', [OrdenController::class, 'listadoOrdenGiradasDeLider']);
        Route::get('orden/pre-presupuestadas-lider/causa/{idCausa}', [OrdenController::class, 'listadoOrdenPrePresupuestadasDeLider']);
        Route::get('orden/presupuestadas-lider/causa/{idCausa}', [OrdenController::class, 'listadoOrdenPresupuestadasDeLider']);
        Route::get('orden/aceptadas-lider/causa/{idCausa}', [OrdenController::class, 'listadoOrdenAceptadasDeLider']);
        Route::get('orden/dinero-entregado-lider/causa/{idCausa}', [OrdenController::class, 'listadoOrdenDineroEntregadoDeLider']);
        Route::get('orden/lista-realizar-lider/causa/{idCausa}', [OrdenController::class, 'listadoOrdenListaRealizarDeLider']);
        Route::get('orden/descargadas-lider/causa/{idCausa}', [OrdenController::class, 'listadoOrdenDescargadasDeLider']);
        Route::get('orden/pronuncio-abogado-lider/causa/{idCausa}', [OrdenController::class, 'listadoOrdenPronuncioAbogadoDeLider']);
        Route::get('orden/cuenta-conciliada-lider/causa/{idCausa}', [OrdenController::class, 'listadoOrdenCuentaConciliadasDeLider']);
        Route::get('orden/vencidas-leves-lider/causa/{idCausa}', [OrdenController::class, 'listadoOrdenVencidasLevesDeLider']);
        Route::get('orden/vencidas-graves-lider/causa/{idCausa}', [OrdenController::class, 'listadoOrdenVencidasGravesDeLider']);
        Route::get('orden/listado-para-pago-procurador/{procuradorId}', [OrdenController::class, 'ordenesListaOrdenCerradasParaPagoProcurador']);
        Route::get('orden/listado-por-pisos', [OrdenController::class, 'listarOrdenPorPisos']);
        Route::get('orden/listado-por-urgencias', [OrdenController::class, 'listadoOrdenPorUrgencias']);
        Route::get('orden/listado-ejecutar', [OrdenController::class, 'listadoOrdenEjecutar']);
        Route::get('orden/sumatoria-gastos-fecha/{causaId}/{fechaCierre}', [OrdenController::class, 'sumatoriaGastoPorCausaYFecha']);
        Route::get('orden/detalle-financiero/causa/{causaId}', [OrdenController::class, 'listadoDetalleFinancieroCausa']);
        //Cotizacion

        //Presupuesto
        Route::get('presupuestos', [PresupuestoController::class, 'index']);
        Route::post('presupuestos', [PresupuestoController::class, 'store']);
        Route::get('presupuestos/{presupuesto}', [PresupuestoController::class, 'show']);
        Route::patch('presupuestos/{presupuesto}', [PresupuestoController::class, 'update']);
        Route::patch('presupuestos/eliminar/{presupuesto}', [PresupuestoController::class, 'destroy']);
        Route::patch('presupuestos/entregar/{presupuesto}', [PresupuestoController::class, 'entregarPresupuesto']);
        Route::post('presupuestos/entregar-masivo', [PresupuestoController::class, 'entregarPresupuestosMasivo']);
        //Procuraduria Descarga
        Route::get('descargas', [ProcuraduriaDescargaController::class, 'index']);
        Route::post('descargas', [ProcuraduriaDescargaController::class, 'store']);
        Route::get('descargas/ultima-foja/causa/{causaId}', [ProcuraduriaDescargaController::class, 'ultinaFojaCausa']);

        //Confirmacion
        Route::patch('confirmacion/pronuncio-abogado/{confirmacion}', [ConfirmacionController::class, 'pronuncioAbogado']);
        Route::patch('confirmacion/pronuncio-contador/{confirmacion}', [ConfirmacionController::class, 'pronuncioContador']);
        Route::post('confirmacion/pronuncio-contador/devolucion-masivo', [ConfirmacionController::class, 'devolucionSaldoMasivo']);
        // Final Costo
        Route::patch('finalcostos/costo-judicial-venta/{finalCosto}', [FinalCostoController::class, 'colocarCostoJudicialVenta']);
        //Gestion Alternativa
        Route::post('gestion-alternativa', [GestionAlternativaController::class, 'store']);
        Route::patch('gestion-alternativa/{gestionAlternativa}', [GestionAlternativaController::class, 'update']);
        Route::patch('gestion-alternativa/eliminar/{gestionAlternativa}', [GestionAlternativaController::class, 'destroy']);
        Route::get('gestion-alternativa/orden/{ordenId}', [GestionAlternativaController::class, 'obtenerPorOrdenId']);
        Route::get('gestion-alternativa/obtener/{gestionId}', [GestionAlternativaController::class, 'obtenerUnoById']);
        Route::get('gestion-alternativa/otras-gestiones/{gestionId}/{ordenId}', [GestionAlternativaController::class, 'contarGestionesPosteriores']);
        
        //Paquetes
        Route::get('paquetes', [PaqueteController::class, 'index']);
        Route::get('paquetes/listado/segun-usuario', [PaqueteController::class, 'listadoPaquetesSegunUsuario']);
        Route::post('paquetes', [PaqueteController::class, 'store']);
        Route::patch('paquetes/{paquete}', [PaqueteController::class, 'update']);
        Route::patch('paquetes/eliminar/{paquete}', [PaqueteController::class, 'destroy']);
        Route::get('paquetes/{paquete}', [PaqueteController::class, 'show']);
        //Compra paquetes
        Route::post('compra-paquetes', [CompraPaqueteController::class, 'store']);
        Route::get('compra-paquetes', [CompraPaqueteController::class, 'index']);
        Route::get('compra-paquetes/{compraPaquete}', [CompraPaqueteController::class, 'show']);
        Route::get('compra-paquetes/lista/activos', [CompraPaqueteController::class, 'listarActivosPorUsuario']);

        //Documentos Categorias
        Route::get('documentos-categorias', [DocumentosCategoriaController::class, 'index']);
        Route::get('documentos-categorias/tramites', [DocumentosCategoriaController::class, 'indexTramites']);
        Route::get('documentos-categorias/normas', [DocumentosCategoriaController::class, 'indexNormas']);
        Route::post('documentos-categorias', [DocumentosCategoriaController::class, 'store']);
        Route::get('documentos-categorias/listar/{documentosCategoria?}', [DocumentosCategoriaController::class, 'show']);
        Route::patch('documentos-categorias/{documentosCategoria}', [DocumentosCategoriaController::class, 'update']);
        Route::patch('documentos-categorias/eliminar/{documentosCategoria}', [DocumentosCategoriaController::class, 'destroy']);

        Route::get('documentos-categorias/categoria/listado', [DocumentosCategoriaController::class, 'listarCategorias']);

        //Documentos - NORMAS-TRAMITES
        Route::get('documentos', [DocumentoController::class, 'index']);
        Route::get('documentos/tramites', [DocumentoController::class, 'indexTramites']);
        Route::get('documentos/normas', [DocumentoController::class, 'indexNormas']);
        Route::post('documentos', [DocumentoController::class, 'store']);
        Route::post('documentos/{documento}', [DocumentoController::class, 'update']);
        Route::patch('documentos/eliminar/{documento}', [DocumentoController::class, 'destroy']);
        //Tabla config
        Route::post('tabla-config/actualizar', [TablaConfigController::class, 'update']);
        Route::post('tabla-config/actualizar-arancel', [TablaConfigController::class, 'updataArancelesAbogado']);
        Route::post('tabla-config/actualizar-acuerdos', [TablaConfigController::class, 'updataAcuerdosUsuarios']);
        //Paquete Causas
        Route::post('paquete-causas', [PaqueteCausaController::class, 'store']);
        Route::get('paquete-causas/listado/compra-pquete/{compraPaqueteId}', [PaqueteCausaController::class, 'listadoActivosDeUnPaquete']);
        Route::patch('paquete-causas/eliminar/{paqueteCausa}', [PaqueteCausaController::class, 'destroy']);
        //Video
        Route::get('videos', [VideoController::class, 'index']);
        Route::get('videos/procuradores', [VideoController::class, 'indexProcuradores']);
        Route::get('videos/abogados', [VideoController::class, 'indexAbogados']);
        Route::post('videos', [VideoController::class, 'store']);
        Route::get('videos/{video}', [VideoController::class, 'show']);
        Route::patch('videos/{video}', [VideoController::class, 'update']);
        Route::patch('videos/eliminar/{video}', [VideoController::class, 'destroy']);
        Route::get('videos/listado/usuarios', [VideoController::class, 'listarActivosSegunUsuario']);
        //Billetera
        Route::get('billetera/abogado/{abogadoId}', [BilleteraController::class, 'obtenerPorAbogadoId']);
        Route::get('billetera/listado-usuarios', [BilleteraController::class, 'listarConUsuarios']);
        //Billetera Transacciones
        Route::get('billetera-transaccion', [BilleteraTransaccionController::class, 'index']);
        Route::get('billetera-transaccion/listado-billetera/{fechaIni}/{fechaFin}/{billeteraId}', [BilleteraTransaccionController::class, 'listadoPorBilletera']);
        Route::post('billetera-transaccion', [BilleteraTransaccionController::class, 'store']);
        Route::patch('billetera-transaccion/eliminar/{billeteraTransaccion}', [BilleteraTransaccionController::class, 'destroy']);
        Route::get('billetera-transaccion/listado-dep-admin', [BilleteraTransaccionController::class, 'DepBilleteraDesdeAdmin']);
        //Transacciones Causas
        Route::post('transacciones-causas', [TransaccionesCausaController::class, 'store']);
        Route::post('transacciones-causas/devolucion', [TransaccionesCausaController::class, 'devolucionABGeneral']);
        Route::get('transacciones-causas/listado-causa/{causaId}', [TransaccionesCausaController::class, 'obtenerTransaccionesDeCausa']);
        Route::get('transacciones-causas/depositos/{causaId}', [TransaccionesCausaController::class, 'obtenerDepositosDeCausa']);
        Route::get('transacciones-causas/trn-send-receib/{causaId}', [TransaccionesCausaController::class, 'trnEnvioRecibidoCausa']);
        //Parametro vigencia
        Route::get('parametro-vigencias/obtener', [ParametroVigenciaController::class, 'obtenerUnoUsuario']);
        //Transacciones admin
        Route::get('transacciones-admin/listado', [TransaccionesAdminController::class, 'obtenerTransaccionesDeAdmin']);
        Route::post('transacciones-admin/trn-contador', [TransaccionesAdminController::class, 'depositoAContador']);
        //Transacciones contador
        Route::get('transacciones-contador/listado', [TransaccionesContadorController::class, 'obtenerTransaccionesDeContador']);
        Route::post('transacciones-contador/devolucion-admin', [TransaccionesContadorController::class, 'devolucionAAdmin']);
        //ProcuradorPago
        Route::get('procurador-pago', [ProcuradorPagoController::class, 'index']);
        Route::post('procurador-pago', [ProcuradorPagoController::class, 'store']);
        Route::post('procurador-pago/extraordinario', [ProcuradorPagoController::class, 'pagoExtraordinario']);
        Route::get('procurador-pago/ultimo-pago/{procuradorId}', [ProcuradorPagoController::class, 'obtenerUltimoPagoDeProcurador']);
        Route::get('procurador-pago/listado/{procuradorId}', [ProcuradorPagoController::class, 'obtenerPagosDeUnProcurador']);
        //Retiros
        Route::post('retiro', [RetiroController::class, 'store']);
        Route::get('retiro', [RetiroController::class, 'index']);
        //Registro llamadas
        Route::post('registro-llamadas', [RegistroLlamadaController::class, 'store']);
        Route::get('registro-llamadas/por-gestion/{gestionId}', [RegistroLlamadaController::class, 'obtenerPorGestionId']);
        //Notificacion
        Route::post('notificacion', [NotificacionController::class, 'store']);
        Route::get('notificacion', [NotificacionController::class, 'index']);
        Route::patch('notificacion/{notificacion}', [NotificacionController::class, 'update']);
        Route::patch('notificacion/eliminar/{notificacion}', [NotificacionController::class, 'destroy']);
    });
});
