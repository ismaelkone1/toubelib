<?php


use Psr\Container\ContainerInterface;
use toubeelib\application\actions\ConsulterRendezVousAction;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RendezVousRepositoryInterface;
use toubeelib\core\services\praticien\ServicePraticien;
use toubeelib\core\services\praticien\ServicePraticienInterface;
use toubeelib\core\services\rdv\ServiceRendezVous;
use toubeelib\core\services\rdv\ServiceRendezVousInterface;
use toubeelib\infrastructure\repositories\ArrayPraticienRepository;
use toubeelib\infrastructure\repositories\ArrayRdvRepository;

return [


    ConsulterRendezVousAction::class => function (ContainerInterface $c) {
        return new ConsulterRendezVousAction($c->get(ServiceRendezVousInterface::class));
    },

    ServiceRendezVousInterface::class => function (ContainerInterface $c) {
        return new ServiceRendezVous($c->get(RendezVousRepositoryInterface::class) , $c->get(PraticienRepositoryInterface::class));
    },

    RendezVousRepositoryInterface::class => function (ContainerInterface $c) {
        return new ArrayRdvRepository();
    },

    ServicePraticienInterface::class => function (ContainerInterface $c) {
        return new ServicePraticien($c->get(PraticienRepositoryInterface::class));
    },

    PraticienRepositoryInterface::class => function (ContainerInterface $c) {
        return new ArrayPraticienRepository();
    }
];