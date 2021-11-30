<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Services\RequestService;

class MoviesController extends AbstractController {

    // Function to return proper JSON
    private function ajson(Array $data): Response {
        $json = $this->get("serializer")->serialize($data, "json");

        $response = new Response();
        $response->setContent($json);
        $response->headers->set("Content-Type", "application/json");
        $response->setStatusCode($data['status']);

        return $response;
    }

    public function index() {
        return $this->ajson([
            "message" => "Index of movies controller",
            "path" => "src/Controller/MoviesController.php",
        ]);
    }

    public function getPopularMovies(Request $request): Response {
        $rs = new RequestService();

        $query_params = [
            "api_key" => $_ENV["API_KEY"],
            "page" => $request->query->get("page", 1),
            "language" => $request->query->get("lang", "es-ES"),
        ];

        $body_params = [];

        $url = "{$_ENV["API_PATH"]}/movie/popular";
        $cURL_response = $rs->sendRequest($url, "GET", $query_params, $body_params);

        return $this->ajson($cURL_response);
    }

    public function getMoviesBySearch(Request $request): Response {
        $rs = new RequestService();

        $query_params = [
            "api_key" => $_ENV["API_KEY"],
            "page" => $request->query->get("page", 1),
            "language" => $request->query->get("lang", "es-ES"),
            "query" => $request->query->get("query", ""),
        ];

        $body_params = [];

        $url = "{$_ENV["API_PATH"]}/search/movie";
        $cURL_response = $rs->sendRequest($url, "GET", $query_params, $body_params);

        return $this->ajson($cURL_response);
    }

    public function getMovie(Request $request, int $id = 0): Response {
        $rs = new RequestService();

        $query_params = [
            "api_key" => $_ENV["API_KEY"],
            "language" => $request->query->get("lang", "es-ES"),
        ];

        $body_params = [];

        $url = "{$_ENV["API_PATH"]}/movie/$id";
        $cURL_response = $rs->sendRequest($url, "GET", $query_params, $body_params);

        return $this->ajson($cURL_response);
    }

    public function voteMovie(Request $request, int $id = 0): Response {
        $rs = new RequestService();

        $query_params = [
            "api_key" => $_ENV["API_KEY"],
            "session_id" => $_ENV["API_SESSION"]
        ];

        // For POST, we need to get the content of the request like this, otherwise it will be empty in the request array (either that or installing FOSRestBundle)
        // https://stackoverflow.com/a/54052171
        // Config (in Spanish): https://www.youtube.com/watch?v=xPjpoC1BNII
        // $data = json_decode($request->getContent(), true);
        
        $body_params = [
            "value" => $request->request->get("value", null),
        ];

        $url = "{$_ENV["API_PATH"]}/movie/$id/rating";

        $cURL_response = $rs->sendRequest($url, "POST", $query_params, $body_params);

        return $this->ajson($cURL_response);
    }
}
