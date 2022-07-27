<?php

namespace App\Controller\Admin;

use App\Entity\Audio;
use App\Entity\Country;
use App\Entity\FilmByProvider;
use App\Entity\FilmByProviderTranslation;
use App\Entity\Genre;
use App\Entity\Image;
use App\Entity\People;
use App\Entity\Provider;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->redirectToRoute('main');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle("Start Parser");
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Audio', 'fas fa-film', Audio::class);
        yield MenuItem::linkToCrud('Image', 'fas fa-film', Image::class);
        yield MenuItem::linkToCrud('Country', 'fas fa-film', Country::class);
        yield MenuItem::linkToCrud('FilmByProvider', 'fas fa-film', FilmByProvider::class);
        yield MenuItem::linkToCrud('FilmByTranslation', 'fas fa-film', FilmByProviderTranslation::class);
        yield MenuItem::linkToCrud('Genre', 'fas fa-film', Genre::class);
        yield MenuItem::linkToCrud('People', 'fas fa-film', People::class);
        yield MenuItem::linkToCrud('Provider', 'fas fa-film', Provider::class);
        yield MenuItem::linkToCrud('Image', 'fas fa-film', Image::class);
    }
}
