<?php

/**
 * PHP version 7
 *
 * @category BlogRepository
 *
 * @author <jcmorodiaz@gmail.com>
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BlogRepository extends EntityRepository
{
    /**
     * Obtiene un post.
     *
     * @param string $slug slug
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findPost($slug)
    {
        $em = $this->getEntityManager();

        $dql = 'select l from AppBundle:Blog l
                where l.slug = :slug';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('slug', $slug);
        $consulta->useResultCache(true, 600);

        return $consulta->getOneOrNullResult();
    }

    /**
     * Obtiene post verificados.
     *
     * @param int $limit numero maximo de resultados
     *
     * @return array
     */
    public function findTodosLosPostVerificados(int $limit = 10): array
    {
        $em = $this->getEntityManager();

        $dql = 'select l from AppBundle:Blog l
                where l.revisada = true
                and l.fechaPublicacion <= :fechaHoy
                order by l.fechaPublicacion desc';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('fechaHoy', new \DateTime('now'));
        $consulta->setMaxResults($limit);
        $consulta->useResultCache(true, 600);

        return $consulta->getResult();
    }

    /**
     * Obtiene todos los recent post verificados, menos el indicado.
     *
     * @param string $slug  slug
     * @param int    $limit numero maximo de resultados
     *
     * @return array
     */
    public function recentPostDetalleVerificados(string $slug, int $limit): array
    {
        $em = $this->getEntityManager();

        $dql = 'select l from AppBundle:Blog l
                where l.revisada = true
                and l.slug != :slug
                and l.fechaPublicacion <= :fechaHoy
                order by l.fechaPublicacion desc';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('slug', $slug);
        $consulta->setParameter('fechaHoy', new \DateTime('now'));
        $consulta->setMaxResults($limit);
        $consulta->useResultCache(true, 600);

        return $consulta->getResult();
    }

    /**
     * Obtiene recent post verificados, menos el indicado.
     *
     * @param int $limit  numero maximo de resultados
     * @param int $offset pila a partir del cual se inicia la busqueda
     *
     * @return array
     */
    public function recentPostVerificados($limit, $offset = 0): array
    {
        $em = $this->getEntityManager();

        $dql = 'select l from AppBundle:Blog l
                where l.revisada = true
                and l.fechaPublicacion <= :fechaHoy
                order by l.fechaPublicacion desc';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('fechaHoy', new \DateTime('now'));
        $consulta->setMaxResults($limit);
        $consulta->setFirstResult($offset);
        $consulta->useResultCache(true, 600);

        return $consulta->getResult();
    }

    /**
     * Obtiene todos los post de una categoria.
     *
     * @param string $slug slug
     *
     * @return array
     */
    public function findPostByCategoria($slug): array
    {
        $em = $this->getEntityManager();

        $dql = 'select l from AppBundle:Blog l
                JOIN l.categoria c
                where l.revisada = true
                and c.slug = :slug
                and l.fechaPublicacion <= :fechaHoy
                order by l.fechaPublicacion desc';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('fechaHoy', new \DateTime('now'));
        $consulta->setParameter('slug', $slug);
        $consulta->useResultCache(true, 600);

        return $consulta->getResult();
    }

    /**
     * Obtiene los post de una determinada fecha (Actualmente del mes).
     *
     * @param int $anyo año
     * @param int $mes  mes
     *
     * @return array
     *
     * @throws \Exception
     */
    public function findPostByFecha($anyo, $mes): array
    {
        $em = $this->getEntityManager();

        $dql = 'select l from AppBundle:Blog l
                where l.revisada = true
                and l.fechaPublicacion >= :fechaInicio
                and l.fechaPublicacion < :fechaFin
                and l.fechaPublicacion <= :fechaHoy
                order by l.fechaPublicacion desc';

        $consulta = $em->createQuery($dql);

        $fechaInicio = '01-'.$mes.'-'.$anyo;
        $dateTimeInicio = new \DateTime($fechaInicio);
        $dateTimeFin = new \DateTime($fechaInicio);
        $dateTimeFin->add(new \DateInterval('P1M'));
        $consulta->setParameter('fechaInicio', $dateTimeInicio);
        $consulta->setParameter('fechaFin', $dateTimeFin);
        $consulta->setParameter('fechaHoy', new \DateTime('now'));
        $consulta->useResultCache(true, 600);

        return $consulta->getResult();
    }

    /**
     * Obtiene lista de post que contienen $needle en titulo, cuerpo o resumen.
     *
     * @param string $needle cadena de busqueda
     *
     * @return array
     */
    public function searchPost($needle): array
    {
        $em = $this->getEntityManager();

        $dql = 'select l from AppBundle:Blog l
                where l.revisada = true
                and l.fechaPublicacion <= :fechaHoy
                and (l.titulo like :needle or l.resumen like :needle or l.cuerpo like :needle)
                order by l.fechaPublicacion desc';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('needle', '%'.$needle.'%');
        $consulta->setParameter('fechaHoy', new \DateTime('now'));
        $consulta->useResultCache(true, 3600);

        return $consulta->getResult();
    }

    /**
     * Obtiene todos los post de una etiqueta.
     *
     * @param string $slug slug
     *
     * @return array
     */
    public function findPostByEtiqueta($slug): array
    {
        $em = $this->getEntityManager();

        $dql = 'select l from AppBundle:Blog l
                join l.etiquetas e
                where e.slug = :slug 
                and l.revisada = true
                and l.fechaPublicacion <= :fechaHoy 
                order by l.fechaPublicacion desc';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('fechaHoy', new \DateTime('now'));
        $consulta->setParameter('slug', $slug);
        $consulta->useResultCache(true, 600);

        return $consulta->getResult();
    }
}
