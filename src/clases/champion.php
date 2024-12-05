<?php

    // CLASE PARA CADA CAMPEÓN

    class champion{

        public $nombre;
        public $id;
        public $masteryPoints;
        public $masteryLevel;
        public $foto;


        /**
         * Constructor para la clase champion
         *
         * @param [type] $id
         * @param [type] $masteryPoints
         * @param [type] $masteryLevel
         */
        public function __construct( $id, $masteryPoints, $masteryLevel)
        {
            
            $this->id = $id;
            $this->masteryPoints = $masteryPoints;
            $this->masteryLevel = $masteryLevel;
            $this->traerNombre();
            $this->setFotoLoading();
            
       
            
        }

        // SETTERS Y GETTERS DE LA CLASE champion
        public function getId(){
            return $this->id;
        }
        
        public function getMasteryPoints(){
            return $this->masteryPoints;
        }

        public function getMasteryLevel(){
            return $this->masteryLevel;
        }
        public function getNombre(){
            return $this->nombre;
        }
        public function setNombre($nombre){
            $this->nombre = $nombre;
        }
        public function getFotoLoading(){
            return $this->foto;
        }
       

        /**
         * Hace una consulta a la tabla champion de la Base de Datos, 
         * se trae el campo id de la fila donde key sea igual que la propiedad id
         * del objeto
         * 
         *
         * @return void
         */
        public function traerNombre(){
            $nombre = "hola";
            $conn = new mysqli(
                SERVERNAME,
                USERNAME,
                PASSWORD,
                DBNAME
            );
            
            $sql = "SELECT `id` FROM champions WHERE `key` = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $this->id);
            $stmt->execute();
            $stmt->bind_result($nombre);

            if ($stmt->fetch()) {
                $this->setNombre($nombre); // Asignar el valor obtenido a la propiedad
            } else {
                $this->setNombre(null); // Si no se encuentra resultado, asignar null o manejar el caso
            }

            $stmt->close();
            $conn->close();
        }


        public function setFotoLoading(){

            $url = "https://ddragon.leagueoflegends.com/cdn/img/champion/loading/{$this->getNombre()}_0.jpg";

            $this->foto = $url;


        }
        public function pintarFotoLoading(){

            $foto = '<img src="' . $this->getFotoLoading() . '"> </img>';

            return $foto;

        }
        



    }



?>