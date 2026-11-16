<?php
require 'auth.php';
require 'db_config.php';
require_role('association');

$assoc_id = $_SESSION['user_id'];
$search = $_GET['search'] ?? '';
$cat_filter = $_GET['category'] ?? '';

// 2. ACTIONS : réserver une donation ou une offre (uniquement si elle est « approved »)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'], $_POST['reserve_id'])) {
    $id = (int)$_POST['reserve_id'];
    $table = ($_POST['action_type'] === 'job') ? 'job_offers' : 'items';
    $update = $pdo->prepare("UPDATE $table SET status = 'reserved', assoc_id = ? WHERE id = ? AND status = 'approved'");
    $update->execute([$assoc_id, $id]);
    header("Location: association_dashboard.php?success=" . ($update->rowCount() ? '1' : '0'));
    exit();
}

// 3. FETCH DATA: Separate queries for Donations and Jobs
// Items Query
$item_q = "SELECT * FROM items WHERE status = 'approved'";
$item_p = [];
if ($search) { $item_q .= " AND item_name LIKE ?"; $item_p[] = "%$search%"; }
if ($cat_filter) { $item_q .= " AND category = ?"; $item_p[] = $cat_filter; }
$stmt_items = $pdo->prepare($item_q . " ORDER BY created_at DESC");
$stmt_items->execute($item_p);
$approved_items = $stmt_items->fetchAll();

// Jobs Query
$job_q = "SELECT * FROM job_offers WHERE status = 'approved'";
$job_p = [];
if ($search) { $job_q .= " AND (job_title LIKE ? OR company LIKE ?)"; $job_p[] = "%$search%"; $job_p[] = "%$search%"; }
$stmt_jobs = $pdo->prepare($job_q . " ORDER BY created_at DESC");
$stmt_jobs->execute($job_p);
$approved_jobs = $stmt_jobs->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Association Hub | Hand2Hand</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: #05080f; color: white;
            background-image: radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.05) 0px, transparent 50%);
        }
        .glass-card { background: rgba(17, 24, 39, 0.4); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.03); }
        .glass-input { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); color: white; }
    </style>
</head>
<body class="pt-24 pb-20">
    <?php include 'header.php'; ?>

    <main class="max-w-7xl mx-auto px-6">
        <div class="mb-16">
            <h1 class="text-5xl font-black uppercase tracking-tighter">
                Welcome, <span class="text-emerald-500"><?php echo htmlspecialchars($_SESSION['display_name']); ?></span>
            </h1>
            
            <?php if (isset($_GET['success'])): ?>
            <div class="mt-6 p-4 rounded-xl border text-sm font-semibold <?php echo $_GET['success'] === '1' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-red-500/10 border-red-500/30 text-red-400'; ?>">
                <?php echo $_GET['success'] === '1' ? 'Réservation effectuée avec succès !' : 'Cet élément n\'est plus disponible.'; ?>
            </div>
            <?php endif; ?>

            <form method="GET" class="glass-card p-2 rounded-[2rem] flex flex-col md:flex-row gap-2 mt-8">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Search resources..." class="glass-input flex-grow p-4 px-6 rounded-2xl">
                
                <select name="category" class="glass-input p-4 px-6 rounded-2xl font-semibold">
                    <option value="">All Categories</option>
                    <option value="Food" <?php echo $cat_filter === 'Food' ? 'selected' : ''; ?>>Food</option>
                    <option value="Clothes" <?php echo $cat_filter === 'Clothes' ? 'selected' : ''; ?>>Clothes</option>
                    <option value="Medical" <?php echo $cat_filter === 'Medical' ? 'selected' : ''; ?>>Medical</option>
                    <option value="Other" <?php echo $cat_filter === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>

                <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-black font-black px-10 py-4 rounded-2xl transition-all">
                    FILTER
                </button>
            </form>
        </div>

        <div class="mb-20">
            <div class="flex items-center gap-4 mb-8">
                <div class="h-10 w-1.5 bg-emerald-500 rounded-full"></div>
                <h2 class="text-2xl font-bold uppercase tracking-widest text-slate-300">Available Donations</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (empty($approved_items)): ?><p class="text-slate-500 italic col-span-full">Aucune donation disponible pour le moment.</p><?php endif; ?>
                <?php foreach ($approved_items as $item): ?>
                    <div class="glass-card rounded-[2.5rem] overflow-hidden group flex flex-col border-white/5 hover:border-emerald-500/30 transition-all">
                        <div class="relative h-56">
                            <?php if (!empty($item['item_image'])): ?>
                                <img src="uploads/<?php echo e($item['item_image']); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <?php else: ?>
                                <div class="w-full h-full bg-slate-800 flex items-center justify-center text-xs text-slate-500">NO IMAGE</div>
                            <?php endif; ?>
                            <div class="absolute top-4 left-4 bg-emerald-500 text-black text-[10px] font-black px-3 py-1 rounded-full uppercase italic">
                                <?php echo e($item['category']); ?>
                            </div>
                        </div>
                        <div class="p-6 flex-grow">
                            <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($item['item_name']); ?></h3>
                            <p class="text-slate-400 text-sm line-clamp-2 mb-4"><?php echo htmlspecialchars($item['description']); ?></p>
                            <div class="flex gap-4 text-[10px] font-black text-emerald-500 uppercase mb-6">
                                <span>📦 Qty: <?php echo e($item['quantity']); ?></span>
                                <span>✨ <?php echo e($item['item_condition']); ?></span>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="reserve_id" value="<?php echo (int)$item['id']; ?>">
                                <input type="hidden" name="action_type" value="item">
                                <button type="submit" class="w-full bg-white/5 hover:bg-emerald-500 hover:text-black py-4 rounded-xl font-black uppercase text-sm transition-all border border-white/10 hover:border-transparent">
                                    Reserve Donation
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div>
            <div class="flex items-center gap-4 mb-8">
                <div class="h-10 w-1.5 bg-blue-500 rounded-full"></div>
                <h2 class="text-2xl font-bold uppercase tracking-widest text-slate-300">Job Opportunities</h2>
            </div>

            <div class="space-y-4">
                <?php if (empty($approved_jobs)): ?><p class="text-slate-500 italic">Aucune offre disponible pour le moment.</p><?php endif; ?>
                <?php foreach ($approved_jobs as $job): ?>
                    <div class="glass-card p-6 rounded-[2rem] flex flex-col md:flex-row items-center justify-between gap-6 border-l-4 border-l-blue-500">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-400 font-black">JOB</div>
                            <div>
                                <h3 class="text-xl font-bold"><?php echo htmlspecialchars($job['job_title']); ?> <span class="text-slate-500 font-normal">at</span> <?php echo htmlspecialchars($job['company']); ?></h3>
                                <p class="text-sm text-slate-400"><?php echo htmlspecialchars($job['location']); ?></p>
                            </div>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="reserve_id" value="<?php echo (int)$job['id']; ?>">
                            <input type="hidden" name="action_type" value="job">
                            <button type="submit" class="bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-white px-8 py-3 rounded-xl font-bold transition-all border border-blue-500/20">
                                Distribute Job
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</body>
</html>