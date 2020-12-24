## GET STARTED
Ejemplo práctico para autenticar a un usuario usando Json Web Token, necesitaremos<br>
```composer require firebase/php-jwt```
Instaciamos la Clase y llamamos al metodo Login
```
//use App\Core\Jwt;

class AuthController{
  public function login(){ 
          $user = "Andres Paucar";
          $password = "123456";
          $id = 1;
          $data = [
              "id" => $id,
              "user" => $user,
          ];
          $jwt = new Jwt();
          $token = $jwt->generate($data);
          echo $this->json([
              'success' => true,
              'data' => $data,
              '_token' => $token,
          ]);
  }
}
```

Verficiar el token en el Middleware

```
function __construct(){
        $this->headers =  getallheaders();
}
public function handle(Request $request){
        if(array_key_exists('Authorization',$this->headers) && !empty(trim($this->headers['Authorization']))){
            $token = $this->headers['Authorization'] ;
            $checkToken = new Jwt;
            $statusToken = $checkToken -> checkToken($token);
           
            if(!$statusToken['success']){
                echo response()->json($statusToken,403);
                return false;
            }
            
            return $statusToken['success'];
        }
        echo response()->json(["message" => "Acceso denegado"],404);
        return false;
    }
```
