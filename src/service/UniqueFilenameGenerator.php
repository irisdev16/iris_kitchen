<?php

namespace App\service;

class UniqueFilenameGenerator
{

    //je créé une méthode de généraiton de nom unique
    //j'ai passé en paramètres de cette function le nom de l'image et l'extension de l'image
    //je récupère le time du moment en seconde, le nom de l'image hashé
    //je créé une variable imageNewName qui contient un id unique, le nom hashé, le temps du moment en seconde et l'extension, tout ça concaténé


    public function generateUniqueFilename(string $imageOriginalName, string $imageExtension){

        $currentTimestamp = time();
        $nameHashed = hash('sha256', $imageOriginalName);

        $imageNewName = uniqid() . '-' . $nameHashed . '-' . $currentTimestamp . '.' . $imageExtension;

        return $imageNewName;
    }
}

// un test unitaire teste de manière automatique une fonctionnalité : une classe ou plusieurs classes travaillant ensemble (classes isolées de la BDD, du HTML etc)

// un test fonctionnel (e2e) teste aussi de manière automatique une fonctionnalité : mais en imitant le cheminement complet de l'utilisateur, donc charger une page, vérifier
// quand je clique sur le bouton de suppression que l'élément est bien supprimé en BDD etc