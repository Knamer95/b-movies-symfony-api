# B-Movies API

## Introducción

Para este proyecto he usado la versión de `Symfony 5.3`. Utiliza el esqueleto básico, y a partir de éste una serie de paquetes básicos, como `apache-pack`, `FOS/rest-bundle`, `serializer`, etc.

Se trata de una API que sirve de "gateway" entre la herramienta de `React`, y la API de [TheMovieDB](https://www.themoviedb.org/?language=es-ES).

Utiliza 4 endpoints, localizables en `config/routes.yaml` (la función `index` no hace nada).

Para todos ellos, es necesario proveer una API key. Para el endpoint `/movie/{id}/vote`, además requerirá un session_id (o session_guest_id), explicado más abajo.


## Instalación

1) Clonar repositorio (yo usé `WAMP`, por lo que el proyecto está servido dentro de la carpeta `www`)

2) Ir a la carpeta raíz del proyecto

3) Ejecutar el comando `composer install`

Una vez hecho esto, podemos ejecutar el servidor que alojará el proyecto.

Es posible que se necesite habilitar los módulos headers_module y rewrite_module para Apache. Para posibles problemas de CORS, en /public/index.php he definido headers para permitir todas las opciones (GET/POST/PUT/DELETE/OPTIONS/...), desde cualquier origen. Esto solo es recomendable en un entorno de desarrollo, si se quisiese usar esto, se debería corregir para que solo admita los orígenes y opciones deseados.


## Pasos a seguir

1) Crear fichero .env si no está creado en la raíz (se puede crear otro en otra ruta también si se desea)

2) Añadir los campos: 
	- API_KEY (key para hacer requests a la API)
	- API_PATH (Path de la API, en nuestro caso `https://api.themoviedb.org/3`)
	
3) Una vez hecho esto, tendremos acceso a 3 de las 4 operaciones. Para la cuarta (POST), necesitaremos seguir una serie de pasos (detallados [aquí](https://developers.themoviedb.org/3/authentication/how-do-i-generate-a-session-id)

- Solicitar un request_token. Esto se puede hacer de varias formas. Una sería crear una función PHP. Por ejemplo:
	
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

Más sencillo, con Postman: 
Endpoint: https://api.themoviedb.org/3/authentication/token/new
Parámetros: api_key (GET)
	
- Con el request_token, acceder a https://www.themoviedb.org/authenticate/{request_token}
	
- Una vez obtengamos el request_token, podremos proseguir, para generar un session_id. Desde PHP (como anteriormente), sería


```
function getAPISession() {
	$params = ["request_token" => $_ENV['REQUEST_TOKEN']];
	$url = "{$_ENV["API_PATH"]}/authentication/session/new?api_key={$_ENV["API_KEY"]}";

	$rs = $this->sendRequest($url, "POST", $params);
	return $rs;
}
```

Más sencillo, con Postman: 
Endpoint: https://api.themoviedb.org/3/authentication/session/new
Parámetros: api_key (GET), request_token (POST)
	
Con esto obtendremos nuestro session_id. (Se requiere registrarse en la web de TheMovieDB, si no se está ya)
	
4) Ahora que tenemos nuestro session_id, podemos añadirlo al fichero .env también:
	- API_SESSION (necesario para ciertas operaciones)
	

## Conclusiones

La API me ha parecido potente por lo que he leído en la documentación. Viene todo muy bien detallado. Sin embargo, haciendo búsquedas, he visto que hay series que no están bien nombradas, o que no siguen un patrón determinado.

El proyecto me ha servido para recordar cómo funciona una API simple de Symfony, y aprender alguna cosa nueva :).