<?php


namespace App\EventListener;

use App\Entity\Figure;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\AsciiSlugger;
use function Symfony\Component\String\u;

class SlugCreator
{

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Figure) {
            return;
        }
        $slugger = new AsciiSlugger();
        $slug = u($slugger->slug($entity->getName()))->lower();
        $entity->setSlug($slug);

    }

}