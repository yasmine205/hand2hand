<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.html"); exit(); }
$h_name = htmlspecialchars($_SESSION['display_name'] ?? '', ENT_QUOTES, 'UTF-8');
$h_role = htmlspecialchars(ucfirst($_SESSION['role'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<header class="fixed top-0 left-0 right-0 z-50 bg-[#111827]/80 backdrop-blur-md px-8 py-4 flex justify-between items-center border-b border-emerald-500/30">

    <div class="font-black text-emerald-500 text-2xl tracking-tighter uppercase">
        Hand<span class="text-white">2</span>Hand
    </div>

    <div class="flex items-center gap-6">
        <div class="text-right hidden sm:block">
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Logged in as</p>
            <p class="text-sm text-white font-semibold">
                <?php echo $h_name; ?>
                <span class="text-[10px] text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded ml-1">
                    <?php echo $h_role; ?>
                </span>
            </p>
        </div>

        <a href="logout.php" class="text-xs font-black text-red-500 hover:text-red-400 transition-colors uppercase tracking-widest border border-red-500/20 px-4 py-2 rounded-xl hover:bg-red-500/10">
            Logout
        </a>
    </div>
</header>

<div class="h-20"></div>
