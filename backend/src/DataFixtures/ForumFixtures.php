<?php

namespace App\DataFixtures;

use App\Entity\ForumThread;
use App\Entity\ForumPost;
use App\Entity\User;
use App\Entity\Jam;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ForumFixtures extends Fixture implements OrderedFixtureInterface
{
    public function getOrder(): int
    {
        return 6; 
    }

    public function load(ObjectManager $manager): void
    {
        $userRepository = $manager->getRepository(User::class);
        $jamRepository = $manager->getRepository(Jam::class);
        
        $users = $userRepository->findAll();
        $jams = $jamRepository->findAll();

        if (empty($users) || empty($jams)) {
            return;
        }

        
        $generalThread = new ForumThread();
        $generalThread->setTitle('Bienvenue sur le forum Mangroove !');
        $generalThread->setAuthor($users[0]);
        $generalThread->setIsPublic(true);
        $generalThread->setIsAnnouncement(true);
        $generalThread->setPinned(true);
        $manager->persist($generalThread);

      
        $welcomePost = new ForumPost();
        $welcomePost->setThread($generalThread);
        $welcomePost->setAuthor($users[0]);
        $welcomePost->setContent('Bienvenue sur le forum officiel de Mangroove ! 🎮

Ce forum est l\'endroit parfait pour :
- Discuter des game jams en cours et à venir
- Partager vos créations et recevoir des feedbacks
- Poser des questions techniques
- Rencontrer d\'autres développeurs passionnés

N\'hésitez pas à vous présenter et à participer aux discussions !');
        $manager->persist($welcomePost);

        
        $replyPost = new ForumPost();
        $replyPost->setThread($generalThread);
        $replyPost->setAuthor($users[1] ?? $users[0]);
        $replyPost->setParent($welcomePost);
        $replyPost->setContent('Merci pour l\'accueil ! Hâte de participer aux prochaines jams ! 🚀');
        $manager->persist($replyPost);

       
        $techHelpThread = new ForumThread();
        $techHelpThread->setTitle('Aide technique et ressources pour développeurs');
        $techHelpThread->setAuthor($users[1] ?? $users[0]);
        $techHelpThread->setIsPublic(true);
        $manager->persist($techHelpThread);

        $techPost = new ForumPost();
        $techPost->setThread($techHelpThread);
        $techPost->setAuthor($users[1] ?? $users[0]);
        $techPost->setContent('Ce thread est dédié à l\'entraide technique ! 🛠️

Partagez vos :
- Outils de développement favoris
- Liens vers des tutoriels utiles
- Solutions aux problèmes courants
- Conseils d\'optimisation

Ensemble, nous sommes plus forts !');
        $manager->persist($techPost);

        
        if (!empty($jams)) {
            $jamThread = new ForumThread();
            $jamThread->setTitle('Discussion - ' . $jams[0]->getTitle());
            $jamThread->setAuthor($users[2] ?? $users[0]);
            $jamThread->setJam($jams[0]);
            $jamThread->setIsPublic(true);
            $manager->persist($jamThread);

            $jamPost = new ForumPost();
            $jamPost->setThread($jamThread);
            $jamPost->setAuthor($users[2] ?? $users[0]);
            $jamPost->setContent('Salut à tous ! 👋

Qui participe à cette jam ? Je cherche des coéquipiers pour former une équipe !

Mon profil :
- Programmeur Unity/C#
- 3 ans d\'expérience en game dev
- Déjà participé à 5 jams

Si vous êtes intéressés par une collaboration, contactez-moi !');
            $manager->persist($jamPost);

            
            $teamReply1 = new ForumPost();
            $teamReply1->setThread($jamThread);
            $teamReply1->setAuthor($users[3] ?? $users[1]);
            $teamReply1->setParent($jamPost);
            $teamReply1->setContent('Salut ! Je suis artiste 2D, ça m\'intéresse ! 🎨');
            $manager->persist($teamReply1);

            $teamReply2 = new ForumPost();
            $teamReply2->setThread($jamThread);
            $teamReply2->setAuthor($users[0]);
            $teamReply2->setParent($jamPost);
            $teamReply2->setContent('Super initiative ! N\'hésitez pas à créer un channel Discord pour organiser votre équipe 📞');
            $manager->persist($teamReply2);
        }

    
        $showcaseThread = new ForumThread();
        $showcaseThread->setTitle('Showcase - Partagez vos créations !');
        $showcaseThread->setAuthor($users[3] ?? $users[0]);
        $showcaseThread->setIsPublic(true);
        $manager->persist($showcaseThread);

        $showcasePost = new ForumPost();
        $showcasePost->setThread($showcaseThread);
        $showcasePost->setAuthor($users[3] ?? $users[0]);
        $showcasePost->setContent('Voici mon dernier projet ! 🎮

**Nom du jeu :** Pixel Adventure
**Genre :** Plateforme 2D
**Temps de développement :** 48h (weekend jam)
**Outils utilisés :** Godot, Aseprite, Audacity

Le jeu est encore en cours de développement, mais j\'aimerais avoir vos retours sur le gameplay et l\'esthétique !

Lien pour tester : [Demo disponible bientôt]

Qu\'est-ce que vous en pensez ? 🤔');
        $manager->persist($showcasePost);

        $showcaseFeedback = new ForumPost();
        $showcaseFeedback->setThread($showcaseThread);
        $showcaseFeedback->setAuthor($users[2] ?? $users[0]);
        $showcaseFeedback->setParent($showcasePost);
        $showcaseFeedback->setContent('Ça a l\'air prometteur ! J\'aime beaucoup le style pixel art. Hâte de tester la démo ! 🔥');
        $manager->persist($showcaseFeedback);

       
        if (!empty($jams)) {
            $feedbackThread = new ForumThread();
            $feedbackThread->setTitle('Feedback - Forest Survival Adventure');
            $feedbackThread->setAuthor($users[1] ?? $users[0]);
            $feedbackThread->setJam($jams[0]);
            $feedbackThread->setIsPublic(true);
            $manager->persist($feedbackThread);

            $feedbackPost = new ForumPost();
            $feedbackPost->setThread($feedbackThread);
            $feedbackPost->setAuthor($users[1] ?? $users[0]);
            $feedbackPost->setContent('J\'ai testé Forest Survival Adventure et voici mes impressions ! 🌲

**Points positifs :**
✅ Atmosphère immersive
✅ Graphismes soignés
✅ Concept intéressant

**Points à améliorer :**
❌ Interface un peu confuse au début
❌ Manque de tutoriel
❌ Sauvegarde automatique absente

**Note globale :** 8/10

Excellent travail pour une jam ! Continuez comme ça ! 👏');
            $manager->persist($feedbackPost);

            $feedbackReply = new ForumPost();
            $feedbackReply->setThread($feedbackThread);
            $feedbackReply->setAuthor($users[2] ?? $users[0]);
            $feedbackReply->setParent($feedbackPost);
            $feedbackReply->setContent('Merci pour ce retour détaillé ! Je vais travailler sur l\'interface utilisateur pour la prochaine version 🛠️');
            $manager->persist($feedbackReply);
        }

        $manager->flush();
    }
}