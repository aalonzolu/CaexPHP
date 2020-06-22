# CaexPHP

  

### Instanciar

    use Caex\Caex;
    
    $username = "USERNAME";
    
    $key = "caexkey";
    
    $caex = new Caex($username, $key);

  

### Modo Producción

    $caex->production = true;

Si no se espesifica se tomará como Sandbox

### UsarMetodos

  

    $Departamentos = $caex->ObtenerDepartamentos();
    $Municipios= $caex->ObtenerMunicipios();
    $Poblados = $caex->ObtenerPoblados();
