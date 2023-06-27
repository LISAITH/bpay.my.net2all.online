<?php

namespace App\DataFixtures;

use App\Entity\BanquePartenaire;
use App\Entity\Pays;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BanquePartenaireFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $banquePartenaire = new BanquePartenaire();

        $banquePartenaire->setCodeBanque('ECO625');
        $banquePartenaire->setNomBanque('ECOBANK');

        $findCountry = $manager->getRepository(Pays::class)->findOneBy([
            'id' => 1,
        ]);

        $banquePartenaire->setCountry($findCountry);
        $banquePartenaire->setActive(true);
        $banquePartenaire->setContractSigned(true);
        $banquePartenaire->setDateCreation(new \DateTime('now'));
        $banquePartenaire->setDateModification(new \DateTime('now'));
        $banquePartenaire->setNumeroTel('00 00 00 00');
        $banquePartenaire->setEmailAddress('Ecobanque@sigmatech.com');
        $banquePartenaire->setDateContractSigned(new \DateTime('now'));
        $banquePartenaire->setJoinFile(null);


        $banquePartenaire = new BanquePartenaire();

        $banquePartenaire->setCodeBanque('UBAGROUP761');
        $banquePartenaire->setNomBanque('UBA');

        $findCountry = $manager->getRepository(Pays::class)->findOneBy([
            'id' => 1,
        ]);

        $banquePartenaire->setCountry($findCountry);
        $banquePartenaire->setActive(true);
        $banquePartenaire->setContractSigned(true);
        $banquePartenaire->setDateCreation(new \DateTime('now'));
        $banquePartenaire->setDateModification(new \DateTime('now'));
        $banquePartenaire->setNumeroTel('00 00 00 00');
        $banquePartenaire->setEmailAddress('uba@sigmatech.com');
        $banquePartenaire->setDateContractSigned(new \DateTime('now'));
        $banquePartenaire->setJoinFile(null);

        $banquePartenaire = new BanquePartenaire();

        $banquePartenaire->setCodeBanque('NSIA918');
        $banquePartenaire->setNomBanque('NSIA BANQUE');

        $findCountry = $manager->getRepository(Pays::class)->findOneBy([
            'id' => 1,
        ]);

        $banquePartenaire->setCountry($findCountry);
        $banquePartenaire->setActive(true);
        $banquePartenaire->setContractSigned(true);
        $banquePartenaire->setDateCreation(new \DateTime('now'));
        $banquePartenaire->setDateModification(new \DateTime('now'));
        $banquePartenaire->setNumeroTel('00 00 00 00');
        $banquePartenaire->setEmailAddress('nsia@sigmatech.com');
        $banquePartenaire->setDateContractSigned(new \DateTime('now'));
        $banquePartenaire->setJoinFile(null);

        $manager->flush();
    }
}
