<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    //cette méthode me permet d'afficher les résultats d'une recherche par un utilisateur
    //el me retourne un tableau de recettes
    public function findBySearchInTitle(string $search) : array{


        //j'utilise la méthode "createQueryBuilder" pour faire un requête SQL ici en PHP sur mon IDE (plus simple que
        // de faire une recherche SQL).
        //je lui passe en paramètre mes recettes car c'est sur cela que va s'effectuer les recherches
        $queryBuilder = $this->createQueryBuilder('recipe');

        //je fais ici ma requete SQL
        //dans toutes les recette de ma BDD, je veus que tous les titres de recettes tapés dans ma barre de
        // recherche permette a l'utilisateur d'avoir les recettes en question
        //where = ou les titres de mes recettes
        // setParameter = établie des paramètres comme les % pour la recherche
        //getQuery renvoie ma requete SQL
        //je retourne ensuite le résultat de cette requete SQL
        $query = $queryBuilder->select('recipe')
            //ici je mets en paramètre un deux points pour nettoyer ce que l'utilisateur va rechercher et sécuriser
            // la recherche, l'utilisateur ne pourra pas modifier les paramètres SQL
            ->where ('recipe.title LIKE :search')
            //ici le $search enccadré par les pourcentage permet de dire que pour une recette "Mousse au chocolat" si
            // la recherche est "OUSS", alors je tomberai sur tous les résultats qui ont OUSS dans le titre
            ->setParameter('search', '%'.$search.'%')
            ->getQuery();

        return $query->getResult();


    }
}