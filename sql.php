public function listadoSorteos($em, $medios)
    {
        $mediosQuery = implode(',', array_keys($medios));

        //funcion para sacar el listado de todos los sorteos con los campos que necesitamos
        $sql="SELECT DISTINCT usuario.id_usuario as usuario, ge_medio.des_medio as medio, rel_sorteo_usuario.email,
               ( SELECT GROUP_CONCAT( DISTINCT rel_sorteo_usuario.id_sorteo SEPARATOR '|')
                    FROM rel_sorteo_usuario WHERE rel_sorteo_usuario.id_usuario=usuario.id_usuario) as participa,
               ( SELECT COUNT(rel_sorteo_usuario.id_sorteo)
                    FROM rel_sorteo_usuario WHERE rel_sorteo_usuario.id_usuario=usuario.id_usuario) as totalParticipa,
               ( SELECT GROUP_CONCAT( DISTINCT rel_sorteo_usuario.id_sorteo SEPARATOR '|')
                    FROM rel_sorteo_usuario WHERE rel_sorteo_usuario.id_usuario=usuario.id_usuario and rel_sorteo_usuario.ganador = true) as gana,
               ( SELECT COUNT(rel_sorteo_usuario.id_sorteo)
                    FROM rel_sorteo_usuario WHERE rel_sorteo_usuario.id_usuario=usuario.id_usuario and rel_sorteo_usuario.ganador = true) as totalGana
            FROM rel_sorteo_usuario  
            INNER JOIN usuario ON rel_sorteo_usuario.id_usuario = usuario.id_usuario
            INNER JOIN ge_medio ON ge_medio.id_medio = usuario.id_medio
            WHERE ge_medio.id_medio IN (".$mediosQuery.")";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    
    
