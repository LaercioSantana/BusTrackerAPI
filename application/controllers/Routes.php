<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//require APPPATH . '/libraries/REST_Controller.php';
require APPPATH.'/libraries/Jsv4/Validator.php';
require APPPATH.'/libraries/Jsv4/ValidationException.php';

class Routes extends CI_Controller { 
	private $routes = array();
    
    function loadModel(){
        $this->load->model('routes_model', '', TRUE);
    }
    /**
     * @api {get} /routes Requisitar rotas
     * @apiName GetRoutes
     * @apiGroup Routes
     *
     * @apiParam (Parametros de url) {boolean} points Se false, retorna as rotas sem os pontos. 
     *           Se true, retorna com os pontos. Por padrão é false.
     * @apiExample Exemplo de uso:
     *             curl -i http://host/BusTrackerAPI/index.php/routes?points=true
     *
     * @apiSuccess {Integer} id_routes ID unico da rota.
     * @apiSuccess {String} name  Nome da rota.
     * @apiSuccess {String} description Descricao da rota.
     *
     * @apiSuccessExample Resposta successo sem pontos:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *       "id_routes": "1",
     *       "name": "UFC",
     *       "description": "Rota UFC - MED"
     *     },
     *               ...
     *       {
     *       "id_routes": "2",
     *       "name": "UFC 2",
     *       "description": "Rota UFC - UVA"
     *     }
     *    ]
     * @apiSuccessExample Resposta successo com pontos:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "id_routes": 1,
     *         "name": "UFC",
     *         "description": "Rota UFC - MED",
     *         "points": [{"latitude":3.3232, "logitude":3.23232}, ..., {"latitude":2.343, "logitude":1.32} ]
     *       },
     *               ...
     *      ]
     */
    function getRoutes(){
        $this->loadModel();
        $routes = $this->routes_model->index();
        
        //add points into each route
        if($this->input->get("points") === "true"){
            foreach ($routes as $route){
                $route->points = $this->routes_model->getPoints($route->id_routes);
            }
        }

        return $this->makeJsonRespose($routes, 200);
    }
    /**
     * @api {post} /routes Adicionar uma rota
     * @apiName PostRoutes
     * @apiGroup Routes
     *
     * @apiParam {String} name Nome da rota a ser adicionada.
     * @apiParam {String} description Descrição da rota a ser adicionada.
     * @apiParam {array} points Conjuntos de pontos(coordenadas geográfica) que quando ligados formam a rota a ser adicionada. 
     *
     * @apiSuccess (201 - RouteCreated) {Integer} id ID unico da rota.
     *
     * @apiError (400 - InvalidJSON) O json enviado é invalido. Isso pode ocorrer por falta de parametros, erros de tipos e erros de sintaxe. 
     * 
     * @apiParamExample {json} Exemplo de requisição:
     *   {
     *       "name": "UFC",
     *       "description": "UFC - MED2",
     *       "points": [
     *         {
     *           "latitude": -3.3233,
     *           "longitude": 3.4323
     *         },
     *         {
     *           "latitude": -3.32332,
     *           "longitude": 3.23232
     *         }
     *       ]
     *   }
     * @apiSuccessExample Exemplo de respota com sucesso:
     *     HTTP/1.1 201 CREATED
     *     {"id" : 9}
     * @apiErrorExample {json} Exemplo de respota com erro no json da requisição :
     * HTTP/1.1 400 BAD REQUEST
     * [
     *     {
     *       "code": 0,
     *       "dataPath": "",
     *       "schemaPath": "/type",
     *       "message": "Invalid type: null"
     *     }
     *   ]
     */
    function addRoute(){
        $validator = $this->validateJson($this->input->raw_input_stream, APPPATH.'/controllers/Schemas/RoutesAdd.json');
    	if($validator->valid){
            $this->loadModel();
            $input = json_decode($this->input->raw_input_stream);
            $id = $this->routes_model->insertRoute($input);
            
            return $this->makeJsonRespose(["id" => $id], 201); 
        }
        else{
            return $this->makeJsonRespose($validator->errors, 400);
        }
    }
    function validateJson($json, $schemaPath){
       $route = json_decode($json);

       $schema = json_decode(file_get_contents($schemaPath));
       $validator = Jsv4\Validator::validate( $route, $schema);

       return $validator;
        
    }
    function updateRoute($id){
    	echo "Rota {$id} atualizada";
    }
    /**
     * @api {get} /routes/:id Requisitar uma rota
     * @apiParam {Integer} id Users unique ID.
     * @apiName GetRoute
     * @apiGroup Routes
     *
     *
     * @apiSuccess {Integer} id_routes ID unico da rota.
     * @apiSuccess {String} name  Nome da rota.
     * @apiSuccess {String} description Descricao da rota.
     * @apiSuccess {array} points Pontos com latitude e longitude que formam a rota.
     *
     * @apiError (404 - RouteNotFound) {Interger} id ID da rota requisitada não encontrado.
     *
     * @apiSuccessExample Resposta successo:
     *     HTTP/1.1 200 OK
     *     {
     *         "id_routes": 1,
     *         "name": "UFC",
     *         "description": "Rota UFC-BB",
     *         "points": [
     *           {
     *             "latitude": -3.694505,
     *             "longitude": -40.355569
     *           },
     *                       ...
     *           {
     *             "latitude": -3.690045,
     *             "longitude": -40.351126
     *           }
     *         ]
     *       }       
     *     }
     *
     *
     * @apiErrorExample {json} Exemplo de resposta de erro caso não exista rota com o id requisitado :
     * HTTP/1.1 404 NOT FOUND
     *     {
     *       "id": 9
     *     }
     */
    function getRoute($id){
    	$this->loadModel();
        $route = $this->routes_model->getRoute($id);
        if($route != null){
            $route->points = $this->routes_model->getPoints($id);

            return $this->makeJsonRespose($route, 200);
        }

        return $this->makeJsonRespose(["id" => $id], 404);
    }
    function makeJsonRespose($output, $statusCode){
        return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header($statusCode)
                    ->set_output(json_encode($output, JSON_NUMERIC_CHECK));
    }
    /**
     * @api {get} /routes/:id/points Requisitar pontos de um rota
     * @apiName GetPoints
     * @apiGroup Routes
     *
     * @apiParam {Integer} id ID da rota que se requisita os pontos.
     * @apiSuccess {array} points Pontos com latidute e longitude que formam a rota.
     *
     * @apiSuccessExample Resposta successo:
     *     HTTP/1.1 200 OK
     *          [
     *           {
     *             "latitude": 3.694505,
     *             "longitude": -40.355569
     *           },
     *           {
     *             "latitude": -3.693412,
     *             "longitude": -40.353905
     *           },
     *           {
     *             "latitude": -3.691779,
     *             "longitude": -40.354125
     *           },
     *           {
     *             "latitude": -3.690414,
     *             "longitude": -40.354297
     *           },
     *           {
     *             "latitude": -3.690318,
     *             "longitude": -40.353154
     *           },
     *           {
     *             "latitude": -3.690045,
     *             "longitude": -40.351126
     *           }
     *         ]
     */
    function getPoints($id){
        $this->loadModel();
        $points = $this->routes_model->getPoints($id);
        
        if($this->routes_model->getPoints($id) == null )
            return $this->makeJsonRespose(["id" => $id], 404);

        return $this->makeJsonRespose($points, 200);
    }
    /**
     * @api {delete} /routes/:id Deletar uma rota 
     * @apiName DeleteRoutes
     * @apiGroup Routes
     *
     * @apiParam {Integer} id ID da rota a ser deletada.
     * @apiSuccess (204 - RouteDeleted) {Integer} id ID da rota deletada.
     * @apiError (404 - RouteNotFound) {Interger} id ID da rota requisitada não encontrado.
     * 
     * @apiErrorExample {json} Exemplo de resposta de erro caso não exista rota com o id requisitado :
     * HTTP/1.1 404 NOT FOUND
     *     {
     *       "id": 9
     *     }
     */
    function deleteRoute($id){
    	$this->loadModel();
        $statusCode = 404;

        if($this->routes_model->getRoute($id) != null){
            $this->routes_model->deleteRoute($id);
            $statusCode = 204;
        }

        return $this->makeJsonRespose(["id" => $id], $statusCode);
    }
}
