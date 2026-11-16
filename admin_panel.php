<?php
require 'auth.php';
require 'db_config.php';
require_role('admin');

// Fetch Pending Donations with User info
$stmt_items = $pdo->query("SELECT items.*, users.first_name, users.last_name FROM items JOIN users ON items.user_id = users.id WHERE items.status = 'pending' ORDER BY items.created_at DESC");
$pending_items = $stmt_items->fetchAll();

// Fetch Pending Jobs with User info
$stmt_jobs = $pdo->query("SELECT job_offers.*, users.first_name, users.last_name FROM job_offers JOIN users ON job_offers.user_id = users.id WHERE job_offers.status = 'pending' ORDER BY job_offers.created_at DESC");
$pending_jobs = $stmt_jobs->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Approvals | Hand2Hand</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #05080f; 
            color: white;
            background-image: 
                radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.03) 0px, transparent 50%);
        }
        .glass-card { 
            background: rgba(17, 24, 39, 0.4); 
            backdrop-filter: blur(12px); 
            border: 1px solid rgba(255, 255, 255, 0.03); 
        }
        .btn-approve { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .btn-refuse { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; }
    </style>
</head>
<body class="p-6 md:p-12">
    <?php include 'header.php'; ?>

    <div class="max-w-6xl mx-auto pt-10">
        <div class="mb-12">
            <h1 class="text-4xl font-extrabold tracking-tight">PENDING <span class="text-emerald-500">APPROVALS</span></h1>
            <p class="text-slate-400 mt-2 text-lg">Review community donations and job offers before they go live.</p>
        </div>

        <div class="mb-16">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-8 w-1 bg-emerald-500 rounded-full"></div>
                <h2 class="text-xl font-bold uppercase tracking-widest text-slate-300">Donations en attente</h2>
            </div>

            <?php if (count($pending_items) > 0): ?>
                <div class="grid gap-4">
                    <?php foreach ($pending_items as $item): ?>
                        <div class="glass-card p-6 rounded-[2.5rem] flex flex-col md:flex-row items-center justify-between gap-6 transition-all hover:bg-white/[0.02]">
                            <div class="flex items-center gap-6">
                                <div class="w-20 h-20 rounded-2xl bg-slate-800 overflow-hidden border border-white/5 flex-shrink-0">
                                    <?php if (!empty($item['item_image'])): ?>
                                        <img src="uploads/<?php echo e($item['item_image']); ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-[10px] text-slate-600">NO IMAGE</div>
                                    <?php endif; ?>
                                </div>
                                
                                <div>
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-lg font-bold"><?php echo htmlspecialchars($item['item_name']); ?></h3>
                                        <span class="text-[10px] bg-emerald-500/10 text-emerald-500 px-2 py-0.5 rounded font-bold uppercase"><?php echo htmlspecialchars($item['category']); ?></span>
                                    </div>
                                    <p class="text-sm text-slate-400 mt-1">
                                        <span class="text-slate-500">Donor:</span> <?php echo htmlspecialchars($item['first_name'] . ' ' . $item['last_name']); ?>
                                        <span class="mx-2 text-slate-700">•</span>
                                        <span class="text-slate-500">Qty:</span> <?php echo e($item['quantity']); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-3 w-full md:w-auto">
                                <a href="update_status.php?id=<?php echo (int)$item['id']; ?>&action=approved&type=item" class="btn-approve flex-1 md:flex-none text-center px-8 py-3 rounded-xl font-bold text-sm hover:scale-105 transition-transform shadow-lg shadow-emerald-500/20">Approuver</a>
                                <a href="update_status.php?id=<?php echo (int)$item['id']; ?>&action=refused&type=item" class="btn-refuse flex-1 md:flex-none text-center px-8 py-3 rounded-xl font-bold text-sm hover:bg-red-500 hover:text-white transition-all">Refuser</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="glass-card p-12 rounded-[2.5rem] text-center text-slate-500 italic">Aucune donation en attente.</div>
            <?php endif; ?>
        </div>

        <div>
            <div class="flex items-center gap-3 mb-6">
                <div class="h-8 w-1 bg-blue-500 rounded-full"></div>
                <h2 class="text-xl font-bold uppercase tracking-widest text-slate-300">Offres de travail en attente</h2>
            </div>

            <?php if (count($pending_jobs) > 0): ?>
                <div class="grid gap-4">
                    <?php foreach ($pending_jobs as $job): ?>
                        <div class="glass-card p-6 rounded-[2.5rem] flex flex-col md:flex-row items-center justify-between gap-6 transition-all hover:bg-white/[0.02]">
                            <div class="flex items-center gap-6">
                                <div class="w-16 h-16 rounded-2xl bg-blue-500/10 flex items-center justify-center border border-blue-500/20 text-blue-400 font-bold text-xs">JOB</div>
                                <div>
                                    <h3 class="text-lg font-bold"><?php echo htmlspecialchars($job['job_title']); ?> <span class="text-slate-500 font-normal">chez</span> <?php echo htmlspecialchars($job['company']); ?></h3>
                                    <p class="text-sm text-slate-400 mt-1">
                                        <span class="text-slate-500">Posté par:</span> <?php echo htmlspecialchars($job['first_name'] . ' ' . $job['last_name']); ?>
                                        <span class="mx-2 text-slate-700">•</span>
                                        <span class="text-slate-500">Location:</span> <?php echo htmlspecialchars($job['location']); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-3 w-full md:w-auto">
                                <a href="update_status.php?id=<?php echo (int)$job['id']; ?>&action=approved&type=job" class="bg-blue-600 hover:bg-blue-500 flex-1 md:flex-none text-center px-8 py-3 rounded-xl font-bold text-sm hover:scale-105 transition-transform shadow-lg shadow-blue-500/20">Approuver</a>
                                <a href="update_status.php?id=<?php echo (int)$job['id']; ?>&action=refused&type=job" class="btn-refuse flex-1 md:flex-none text-center px-8 py-3 rounded-xl font-bold text-sm hover:bg-red-500 hover:text-white transition-all">Refuser</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="glass-card p-12 rounded-[2.5rem] text-center text-slate-500 italic">Aucune offre de travail en attente.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>