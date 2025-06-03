<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import AppFooter from '@/Components/AppFooter.vue';
</script>

<template>
    <Head title="Politique de confidentialité" />

    <div class="font-sans text-gray-900 dark:text-gray-100 antialiased min-h-screen flex flex-col">
        <div class="flex-1 pt-4 bg-gray-100 dark:bg-gray-900 pb-6">
            <div class="flex flex-col items-center pt-6 sm:pt-0">
                <div>
                    <AuthenticationCardLogo />
                </div>

                <div class="w-full sm:max-w-2xl mt-6 p-6 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg prose dark:prose-invert">
                    <h1>Politique de Confidentialité</h1>

                    <h2>1. Préambule</h2>
                    <p>RemoteFlow accorde une importance particulière à la protection des données personnelles et au respect de la vie privée de ses collaborateurs. La présente politique décrit la manière dont nous collectons, utilisons et protégeons vos données personnelles.</p>

                    <h2>2. Données collectées</h2>

                    <h3>2.1 Types de données</h3>
                    <p>Nous collectons les données suivantes :</p>
                    <ul>
                        <li>Informations d'identification (nom, prénom, email professionnel)</li>
                        <li>Données de connexion (logs, horodatage)</li>
                        <li>Données d'utilisation de la Plateforme</li>
                        <li>Données métier nécessaires à l'exécution de vos fonctions</li>
                    </ul>

                    <h3>2.2 Finalités de la collecte</h3>
                    <p>Ces données sont collectées pour :</p>
                    <ul>
                        <li>Gérer l'accès et l'utilisation de la Plateforme</li>
                        <li>Assurer la sécurité du système</li>
                        <li>Améliorer l'expérience utilisateur</li>
                        <li>Répondre aux obligations légales et réglementaires</li>
                    </ul>

                    <h2>3. Base légale</h2>
                    <p>Le traitement de vos données personnelles repose sur :</p>
                    <ul>
                        <li>L'exécution du contrat de travail</li>
                        <li>Le respect des obligations légales de l'entreprise</li>
                        <li>L'intérêt légitime de l'entreprise pour la sécurité et l'organisation du travail</li>
                        <li>Votre consentement pour certaines fonctionnalités spécifiques</li>
                    </ul>

                    <h2>4. Confidentialité et respect de la vie privée</h2>

                    <h3>4.1 Accès aux données</h3>
                    <p>L'accès aux données personnelles est strictement limité :</p>
                    <ul>
                        <li>Aux collaborateurs habilités</li>
                        <li>Au service RH et à l'administration</li>
                        <li>Aux services informatiques pour la maintenance</li>
                    </ul>

                    <h3>4.2 Protection de la vie privée</h3>
                    <p>Nous nous engageons à :</p>
                    <ul>
                        <li>Ne collecter que les données nécessaires</li>
                        <li>Respecter la séparation entre sphère professionnelle et privée</li>
                        <li>Garantir la confidentialité des échanges professionnels</li>
                        <li>Ne pas utiliser vos données à des fins de surveillance abusive</li>
                    </ul>

                    <h2>5. Sécurité des données</h2>
                    <p>Nous mettons en place les mesures suivantes :</p>
                    <ul>
                        <li>Chiffrement des données sensibles</li>
                        <li>Contrôle d'accès basé sur les rôles</li>
                        <li>Audits de sécurité réguliers</li>
                        <li>Formation du personnel à la protection des données</li>
                    </ul>

                    <h2>6. Conservation des données</h2>
                    <p>Les données sont conservées :</p>
                    <ul>
                        <li>Pendant la durée de votre contrat de travail</li>
                        <li>Pour une durée supplémentaire conforme aux obligations légales</li>
                        <li>Les logs de connexion sont conservés pour une durée maximale de 12 mois</li>
                    </ul>

                    <h2>7. Vos droits</h2>
                    <p>Conformément au RGPD, vous disposez des droits suivants :</p>
                    <ul>
                        <li>Droit d'accès à vos données</li>
                        <li>Droit de rectification</li>
                        <li>Droit à l'effacement (dans les limites légales)</li>
                        <li>Droit à la limitation du traitement</li>
                        <li>Droit à la portabilité des données</li>
                        <li>Droit d'opposition (dans les limites légales)</li>
                    </ul>
                    <p>Pour exercer ces droits, contactez : <a href="mailto:dpo@remoteflow.fr">dpo@remoteflow.fr</a></p>

                    <h2>8. Sous-traitants</h2>
                    <p>Nous pouvons faire appel à des sous-traitants pour certains traitements. Ces sous-traitants sont contractuellement tenus de respecter la confidentialité et la sécurité de vos données.</p>

                    <h2>9. Transferts de données</h2>
                    <p>En principe, vos données restent dans l'Union Européenne. Tout transfert hors UE sera effectué avec les garanties appropriées prévues par le RGPD.</p>

                    <h2>10. Cookies et traceurs</h2>
                    <p>La Plateforme utilise des cookies techniques nécessaires à son fonctionnement. Aucun cookie de traçage publicitaire n'est utilisé.</p>

                    <h2>11. Modification de la politique</h2>
                    <p>Nous nous réservons le droit de modifier cette politique de confidentialité. Toute modification sera communiquée aux utilisateurs avec un préavis raisonnable.</p>

                    <h2>12. Contact</h2>
                    <p>Pour toute question relative à la protection de vos données personnelles :</p>
                    <ul>
                        <li>Responsable informatique : <a href="mailto:lc.gautier@remoteflow.fr">lc.gautier@remoteflow.fr</a></li>
                        <li>DPO : <a href="mailto:dpo@remoteflow.fr">dpo@remoteflow.fr</a></li>
                        <li>Adresse : 97 rue Principale 75001 PARIS</li>
                        <li>Téléphone : 06.06.06.06.06</li>
                    </ul>

                    <h2>13. Autorité de contrôle</h2>
                    <p>Vous avez le droit d'introduire une réclamation auprès de la CNIL :</p>
                    <ul>
                        <li>Site : <a href="https://www.cnil.fr" target="_blank">www.cnil.fr</a></li>
                        <li>Adresse : 3 Place de Fontenoy, TSA 80715, 75334 Paris Cedex 07</li>
                        <li>Téléphone : 01 53 73 22 22</li>
                    </ul>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-8">
                        <em>Dernière mise à jour : 01/10/2025</em>
                    </p>
                </div>
            </div>
        </div>

        <AppFooter />
    </div>
</template>
