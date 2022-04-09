# B-Movies API

## Introduction

For this project, I used `Symfony 5.3`. It uses the basic skeleton, and a few basic packages, such as `apache-pack`, `FOS/rest-bundle`, `serializer`, etc.

It's an API that serves as a "gateway" between the `React` application,  and the [TheMovieDB](https://www.themoviedb.org/?language=es-ES) API.

It uses 4 endpoints, located at `config/routes.yaml` (the `index` endpoint does nothing).

For all of them, it's required to provide an API key. For the `/movie/{id}/vote` endpoint, it will also require a session_id (or session_guest_id), explained below.


## Installation

1) Clone repository (I used `WAMP`, so the project is served inside the `www` folder)

2) Go to the root folder

3) Run the command `composer install`

Once everything is done, we can run the server that will host the project.

It's possible that the headers_module and rewrite_module need to be enabled for Apache. For possible CORS issues, I defined headers to allow every option it `/public/index.php` (GET/POST/PUT/DELETE/OPTIONS/...), from any origin. This is only recommended in a development env. If you wanted to use this config in a production server, you should config it so it only admits the desired origins and options.


## Steps to follow

1) Create an .env file if there is none in the root (can be created elsewhere if desired)

2) Add the variables: 
	- API_KEY (key for the TheMovieDB API requests)
	- API_PATH (Path of the API, in our case `https://api.themoviedb.org/3`)
	
3) Once all this is set up, we will have access to 3 of the 4 operations. For the fourth (POST), we will need to follow a few steps (detailed [here](https://developers.themoviedb.org/3/authentication/how-do-i-generate-a-session-id))

- Request a request_token. This can be done in many ways. One would be creating a PHP function. For example:
	
```
function getAPIRequestToken() {
	$url = "{$_ENV["API_PATH"]}/authentication/token/new";
	$params = ["api_key" => $_ENV["API_KEY"]];

	$rs = $this->sendRequest($url, "GET", $params);

	if ($rs["result"]["request_token"]) {
		$request_token = $rs["result"]["request_token"];
		return $request_token;
	}

	return null;
}
```

Easier, with Postman:

Endpoint: https://api.themoviedb.org/3/authentication/token/new
Params: api_key (GET)
	
- With the request_token, access to https://www.themoviedb.org/authenticate/{request_token}
	
- Once we have verified the user, we will be able to continue, to generate a session_id. From PHP (like before), it'd be:


```
function getAPISession() {
	$params = ["request_token" => $_ENV['REQUEST_TOKEN']];
	$url = "{$_ENV["API_PATH"]}/authentication/session/new?api_key={$_ENV["API_KEY"]}";

	$rs = $this->sendRequest($url, "POST", $params);
	return $rs;
}
```

Easier, with Postman:

Endpoint: https://api.themoviedb.org/3/authentication/session/new
Params: api_key (query params), request_token (POST)

With this we will obtain our session_id. (It's required to register in the TheMovieDB website to authenticate the user, if it's not already)

4) Now that we have our session_id, we can add it to the .env file as well:
	- API_SESSION (needed for certain operations)
	

## Conclusions

The API seems to be powerful and well documented. As a small criticism, some series seem to not be named properly, or don't follow a determined pattern.
