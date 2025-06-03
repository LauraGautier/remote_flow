<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import AppFooter from '@/Components/AppFooter.vue';
</script>

<template>
    <Head title="Conditions Générales d'Utilisation" />

    <div class="font-sans text-gray-900 dark:text-gray-100 antialiased min-h-screen flex flex-col">
        <div class="flex-1 pt-4 bg-gray-100 dark:bg-gray-900 pb-6">
            <div class="flex flex-col items-center pt-6 sm:pt-0">
                <div>
                    <AuthenticationCardLogo />
                </div>

                <div class="w-full sm:max-w-2xl mt-6 p-6 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg prose dark:prose-invert">
                    <h1>Conditions Générales d'Utilisation</h1>

                    <h2>1. Objet</h2>
                    <p>Les présentes Conditions Générales d'Utilisation (CGU) ont pour objet de définir les modalités d'accès et d'utilisation de la plateforme interne de gestion RemoteFlow (ci-après "la Plateforme").</p>

                    <h2>2. Accès à la Plateforme</h2>

                    <h3>2.1 Conditions d'accès</h3>
                    <p>L'accès à la Plateforme est réservé exclusivement aux collaborateurs de RemoteFlow. Chaque utilisateur dispose d'un compte personnel avec identifiant et mot de passe.</p>

                    <h3>2.2 Obligations de l'utilisateur</h3>
                    <p>L'utilisateur s'engage à :</p>
                    <ul>
                        <li>Garder ses identifiants confidentiels</li>
                        <li>Ne pas partager son compte</li>
                        <li>Informer immédiatement l'administrateur en cas de perte ou vol d'accès</li>
                        <li>Utiliser la Plateforme conformément à son usage prévu</li>
                    </ul>

                    <h2>3. Utilisation de la Plateforme</h2>

                    <h3>3.1 Usage autorisé</h3>
                    <p>La Plateforme est destinée exclusivement à un usage professionnel dans le cadre des activités de RemoteFlow.</p>

                    <h3>3.2 Interdictions</h3>
                    <p>Il est notamment interdit :</p>
                    <ul>
                        <li>D'utiliser la Plateforme à des fins personnelles</li>
                        <li>De télécharger ou diffuser des contenus illégaux ou offensants</li>
                        <li>De porter atteinte à la sécurité ou à l'intégrité du système</li>
                        <li>De collecter des données personnelles à des fins non autorisées</li>
                    </ul>

                    <h2>4. Propriété intellectuelle</h2>
                    <p>Tous les contenus, fonctionnalités et design de la Plateforme sont protégés par le droit de la propriété intellectuelle et appartiennent à RemoteFlow.</p>

                    <h2>5. Responsabilité</h2>
                    <p>RemoteFlow s'efforce d'assurer la disponibilité et la sécurité de la Plateforme, mais ne peut garantir un accès ininterrompu. L'entreprise décline toute responsabilité en cas d'interruption de service.</p>

                    <h2>6. Évolution des CGU</h2>
                    <p>RemoteFlow se réserve le droit de modifier les présentes CGU à tout moment. Les utilisateurs seront informés de toute modification significative.</p>

                    <h2>7. Droit applicable</h2>
                    <p>Les présentes CGU sont soumises au droit français.</p>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-8">
                        <em>Dernière mise à jour : 01/10/2025</em>
                    </p>
                </div>
            </div>
        </div>

        <AppFooter />
    </div>
</template>
