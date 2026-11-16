<?php
require 'auth.php';
require_role('user');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Publier offre | Hand2Hand</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { background: #05080f; color: white; }</style>
</head>
<body class="pt-24">
<?php include 'header.php'; ?>
<div class="max-w-2xl mx-auto p-6">
    <h2 class="text-3xl font-bold mb-6 text-blue-500">Publier une Offre d'Emploi</h2>
    <?php if (isset($_GET['error'])): ?><div class="mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm font-semibold">Veuillez remplir tous les champs obligatoires.</div><?php endif; ?>
    <form action="process_job_offer.php" method="POST" class="bg-gray-900 p-8 rounded-3xl border border-white/10 space-y-4">
        <input type="text" name="job_title" placeholder="Intitulé du poste" required class="w-full p-4 rounded-xl bg-white/5 border border-white/10">
        <input type="text" name="company" placeholder="Entreprise" required class="w-full p-4 rounded-xl bg-white/5 border border-white/10">
        <input type="text" name="location" placeholder="Localisation" required class="w-full p-4 rounded-xl bg-white/5 border border-white/10">
        <textarea name="description" placeholder="Description du poste" required class="w-full p-4 rounded-xl bg-white/5 border border-white/10" rows="4"></textarea>
        <button type="submit" class="w-full bg-blue-600 py-4 rounded-full font-bold">Publier l'offre</button>
    </form>
</div>
</body>
</html>