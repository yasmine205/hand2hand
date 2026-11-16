<?php
require 'auth.php';
require 'db_config.php';
require_role('user');   // les associations et l'admin ont leurs propres pages

$user_id = $_SESSION['user_id'];

// Fetch user's donations (items)
$stmt = $pdo->prepare("SELECT *, 'donation' as type FROM items WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$donations = $stmt->fetchAll();

// Fetch user's job offers
$stmt2 = $pdo->prepare("SELECT *, 'job' as type FROM job_offers WHERE user_id = ? ORDER BY created_at DESC");
$stmt2->execute([$user_id]);
$jobs = $stmt2->fetchAll();

// Combine and sort
$all_activities = array_merge($donations, $jobs);
usort($all_activities, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

$total_donations = count($donations);
$pending_count = count(array_filter($all_activities, fn($i) => $i['status'] === 'pending'));
$active_count  = count(array_filter($all_activities, fn($i) => in_array($i['status'], ['approved', 'reserved'], true)));

$flash = '';
if (isset($_GET['donation']))            $flash = 'Donation envoyée ! Elle sera examinée par notre équipe.';
elseif (isset($_GET['update']))          $flash = 'Donation modifiée avec succès.';
elseif (isset($_GET['deleted']))         $flash = 'Donation supprimée.';
elseif (($_GET['success'] ?? '') === 'job_posted') $flash = 'Offre d\'emploi envoyée ! Elle sera examinée par notre équipe.';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Panel | Hand2Hand</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #05080f; 
            color: white;
            background-image: 
                radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(59, 130, 246, 0.02) 0px, transparent 50%);
        }
        .glass-card { 
            background: rgba(17, 24, 39, 0.4); 
            backdrop-filter: blur(12px); 
            border: 1px solid rgba(255, 255, 255, 0.03); 
        }
        .emerald-gradient { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .blue-gradient { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    </style>
</head>
<body class="p-6 md:p-12 pt-24">
    <?php include 'header.php'; ?>
    
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
            <div>
                <h1 class="text-4xl font-extrabold tracking-tight">User <span class="text-emerald-500">Panel</span></h1>
                <p class="text-slate-400 mt-1">Manage your contributions and track their impact.</p>
            </div>
            <div class="flex gap-3">
                <a href="donate.php" class="emerald-gradient px-6 py-3 rounded-xl font-bold hover:scale-105 transition-transform shadow-lg shadow-emerald-500/20 text-white flex items-center gap-2">
                    <span>+</span> New Donation
                </a>
                <a href="offretravail.php" class="blue-gradient px-6 py-3 rounded-xl font-bold hover:scale-105 transition-transform shadow-lg shadow-blue-500/20 text-white flex items-center gap-2">
                    <span>+</span> Job Offer
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="glass-card p-8 rounded-3xl">
                <p class="text-slate-400 text-sm font-semibold">Total Donations</p>
                <p class="text-4xl font-bold mt-1"><?php echo $total_donations; ?></p>
            </div>
            <div class="glass-card p-8 rounded-3xl">
                <p class="text-slate-400 text-sm font-semibold">Pending Approval</p>
                <p class="text-4xl font-bold mt-1 text-yellow-500"><?php echo $pending_count; ?></p>
            </div>
            <div class="glass-card p-8 rounded-3xl">
                <p class="text-slate-400 text-sm font-semibold">Active Impacts</p>
                <p class="text-4xl font-bold mt-1 text-emerald-500"><?php echo $active_count; ?></p>
            </div>
        </div>

        <?php if ($flash): ?>
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm font-semibold"><?php echo e($flash); ?></div>
        <?php endif; ?>

        <h2 class="text-2xl font-bold mb-6">Recent Activity</h2>
        <div class="space-y-4">
            <?php if (empty($all_activities)): ?>
                <div class="glass-card p-12 rounded-[2.5rem] text-center text-slate-500 italic">Aucune activité pour le moment. Faites votre première donation !</div>
            <?php endif; ?>
            <?php foreach ($all_activities as $activity): ?>
                <div class="glass-card p-5 rounded-[2rem] flex items-center justify-between group hover:bg-white/[0.04] transition-all">
                    <div class="flex items-center gap-6">
                        <div class="relative w-20 h-20 flex-shrink-0">
                            <?php if ($activity['type'] === 'donation'): ?>
                                <?php if (!empty($activity['item_image'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($activity['item_image']); ?>" class="w-full h-full object-cover rounded-2xl shadow-xl group-hover:scale-105 transition-transform">
                                <?php else: ?>
                                    <div class="w-full h-full bg-slate-800 rounded-2xl flex items-center justify-center border border-white/5 text-[10px] text-slate-500">NO IMG</div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="w-full h-full bg-blue-500/10 rounded-2xl flex items-center justify-center border border-blue-500/20 text-blue-400 font-bold text-xs">JOB</div>
                            <?php endif; ?>
                        </div>

                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-bold">
                                    <?php echo htmlspecialchars($activity['type'] === 'donation' ? $activity['item_name'] : $activity['job_title']); ?>
                                </h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?php echo $activity['type'] === 'donation' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-blue-500/10 text-blue-400'; ?>">
                                    <?php echo e($activity['type']); ?>
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                ID: #<?php echo str_pad($activity['id'], 5, '0', STR_PAD_LEFT); ?> • <?php echo date('d M Y', strtotime($activity['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <span class="px-4 py-1.5 rounded-full border border-white/5 bg-white/5 font-bold text-[10px] uppercase 
                            <?php echo in_array($activity['status'], ['approved'], true) ? 'text-emerald-500' : ($activity['status'] === 'refused' ? 'text-red-500' : ($activity['status'] === 'reserved' ? 'text-blue-400' : 'text-yellow-500')); ?>">
                            <?php echo e($activity['status']); ?>
                        </span>

                        <?php if ($activity['type'] === 'donation' && $activity['status'] === 'pending'): ?>
                            <div class="flex gap-2">
                                <a href="edit_donation.php?id=<?php echo (int)$activity['id']; ?>" class="p-2 rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20 hover:bg-blue-500/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                                <a href="delete_donation.php?id=<?php echo (int)$activity['id']; ?>" onclick="return confirm('Supprimer cette donation ?');" class="p-2 rounded-lg bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 7h12M9 7V4h6v3m-8 0l1 13h8l1-13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>