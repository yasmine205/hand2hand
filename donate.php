<?php
require 'auth.php';
require_role('user');   // seuls les donateurs peuvent donner
$errors = ['invalid'=>'Veuillez remplir tous les champs.','image'=>'Image invalide (JPG, PNG, GIF ou WEBP).','size'=>'Image trop lourde (5 Mo maximum).'];
$err = $errors[$_GET['error'] ?? ''] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hand2Hand | Donate Item</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #0a0f1c; color: white; font-family: 'Inter', sans-serif; }
        .glass-card { background: rgba(17, 24, 39, 0.7); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .glass-input { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: white; }
        .glass-input:focus { border-color: #10b981; outline: none; }
    </style>
</head>
<body class="pt-20">
    <?php include 'header.php'; ?>
    <div class="max-w-2xl mx-auto p-6">
        <div class="glass-card rounded-3xl p-8 shadow-2xl">
            <h2 class="text-3xl font-bold mb-2">Donate an <span class="text-emerald-500">Item</span></h2>
            <p class="text-slate-400 mb-8">Fill in the details below. Your donation will be reviewed by our team.</p>

            <?php if ($err): ?><div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm font-semibold"><?php echo e($err); ?></div><?php endif; ?>
            <form action="process_donation.php" method="POST" class="space-y-6" enctype="multipart/form-data" class="space-y-6">

            <div>
                <label class="block text-sm font-semibold mb-2 text-emerald-500">Item Photo</label>
                    <div class="relative">
                        <input type="file" name="item_image" accept="image/jpeg,image/png,image/gif,image/webp" required 
                        class="glass-input w-full p-4 rounded-xl file:mr-4 file:py-2 file:px-4 
                          file:rounded-full file:border-0 file:text-sm file:font-semibold 
                          file:bg-emerald-500 file:text-white hover:file:bg-emerald-600">
                    </div>
                    </div>
                <div>
                    <label class="block text-sm font-semibold mb-2 text-emerald-500">Item Name</label>
                    <input type="text" name="item_name" required placeholder="e.g., Winter Jackets" class="glass-input w-full p-4 rounded-xl">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-emerald-500">Category</label>
                        <select name="category" class="glass-input w-full p-4 rounded-xl">
                            <option value="Food">Food</option>
                            <option value="Clothes">Clothes</option>
                            <option value="Medical">Medical</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-emerald-500">Quantity</label>
                        <input type="text" name="quantity" required placeholder="e.g., 5 units / 2kg" class="glass-input w-full p-4 rounded-xl">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-emerald-500">Condition</label>
                        <select name="item_condition" class="glass-input w-full p-4 rounded-xl">
                            <option value="New">New</option>
                            <option value="Like New">Like New</option>
                            <option value="Good">Good</option>
                            <option value="Fair">Fair</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-emerald-500">Contact Phone</label>
                        <input type="tel" name="contact_phone" required placeholder="Your Phone Number" class="glass-input w-full p-4 rounded-xl">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2 text-emerald-500">Extra Details</label>
                    <textarea name="description" rows="3" required placeholder="Anything else the association should know?" class="glass-input w-full p-4 rounded-xl"></textarea>
                </div>

                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-4 rounded-full transition-all">
                    Submit Donation
                </button>
            </form>
        </div>
    </div>

</body>
</html>