<?


    class game{

        public string $iconoRol;



    public function __construct(public string $id, public string $campeonJugado, public string $rolJugado, public string $duracion, public string $mapa, public string $cola, public string $resultado, public string $kills, public string $deaths, public string $assists, public string $visionScore, public string $damageDealt, public string $cs, public string $championEnemigo, public string $fecha)
    {
        $this->transformarRol();
        $this->transformarMapa();
        $this->setIconoRol();
    }

    public function getCampeonJugado(){
        return $this->campeonJugado;
    }
    public function getRolJugado(){
        return $this->rolJugado;
    }
    public function setRolJugado($rol){
        $this->rolJugado = $rol;
    }

    public function getResultado(){
        return $this->resultado;
    }

    public function getMapa(){
        return $this->mapa;
    }
    public function setMapa($mapa){
        $this->mapa = $mapa;
    }

    public function getIconoRol(){
        return $this->iconoRol;
    }
    public function getKills(){
        return $this->kills;
    }

    public function getDeaths(){
        return $this->deaths;
    }

    public function getAssists(){
        return $this->assists;
    }

    public function getChampionEnemigo(){
        return $this->championEnemigo;
    }

    public function getDuracion(){
        return $this->duracion;
    }

    public function getFecha(){
        return $this->fecha;
    }

    public function getCS(){
        return $this->cs;
    }

    public function getVisionScore(){
        return $this->visionScore;
    }

    public function getDamageDealt(){
        return $this->damageDealt;
    }

    public function transformarRol(){
        if ($this->rolJugado == "UTILITY") {
            $this->setRolJugado("support");
        } else if ($this->rolJugado == "MIDDLE"){
            $this->setRolJugado("mid");
        } else if ($this->rolJugado == "BOTTOM"){
            $this->setRolJugado("adc");
        } else {
            $this->setRolJugado(strtolower($this->getRolJugado()));
        }
    }

    public function transformarMapa(){
        if($this->mapa == "CLASSIC"){
            $this->setMapa("Grieta del Invocador");
        }
    }

    public function setIconoRol(){

        $url = "https://s-lol-web.op.gg/images/icon/icon-position-{$this->getRolJugado()}.svg?v=1729058249";

        $this->iconoRol = $url;


    }

    public function getCola(){
        return $this->cola;
    }

    public function __toString()
    {
        return "<br> <br>Nueva Partida: <br> Campeón: {$this->campeonJugado} <br> Rol: {$this->rolJugado} <br> Duración: {$this->duracion} <br> Mapa: {$this->mapa} <br> Cola: {$this->cola}<br> Resultado: {$this->resultado} <br> KDA: {$this->kills}/{$this->deaths}/{$this->assists} <br> Daño a campeones: {$this->damageDealt} <br> Minions asesinados: {$this->cs} <br> Puntuación de visión: {$this->visionScore} <br> VS: {$this->championEnemigo} <br> Fecha: {$this->fecha} <br>";
    }


    public function pintarCard(){

        $champion = $this->getCampeonJugado();

        if($this->getCampeonJugado() == "Wukong") {
            $champion = "MonkeyKing";
        }


        $url = "https://ddragon.leagueoflegends.com/cdn/img/champion/loading/{$champion}_0.jpg";

        

        echo '

         
            <div class="History-Card-Container ' . $this->resultado . '" style="background-image: url(\'' . $url . '\'); background-size:cover; background-position:top;">

            
             
            <span>' . $this->getResultado() . '</span>
            <span>' . $this->getMapa() . '</span>';

            if ($this->getCola() != "ARAM") {
                echo '<span>' . $this->getCola() . ' </span>
                <img src="' . $this->getIconoRol() .  '"></img>
                echo <span class="Span-Champion-Enemigo"><h3>VS: </h3> ' .  $this->getChampionEnemigo(). '</span>
                <span class="Span-Vision"><h3>Visión: </h3>'. $this->getVisionScore() .'</span>

                ';
            }else{
                echo '<span></span>';
            };

            echo '
            
            <span class="Span-Sombreado-Superior"></span>

            <span class="kda"><h3>KDA: </h3> ' . $this->getKills() . '/' . $this->getDeaths() . '/' . $this->getAssists() .'</span>

            <span class="Span-Duracion">' . $this->getDuracion() .'</span>
            <span class="Span-Fecha">' . $this->getFecha() .'</span>
            <span class="Span-CS"><h3>CS: </h3>' . $this->getCS() .'</span>
            <span class="Span-Damage"><h3>Daño: </h3>' . $this->getDamageDealt() . '</span>

      
            
            ';



            
            
            
            
         echo '  
         
         
            
        </div>
        
        
        
        
        ';


    }


    }












?>