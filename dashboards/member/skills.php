<?php
require_once '../../auth/auth.php';
require_once '../../auth/authorize.php';
require_once '../../config/db.php';

authorizeRole(['member']);

$user_id = $_SESSION['user_id'];
$department_id = $_SESSION['department_id'];

/*
Fetch user skills
*/
$stmt = $pdo->prepare("
    SELECT skill_id, skill_description, created_at
    FROM user_skill
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$user_id]);
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="main-content">

<h2>My Skills</h2>

<!-- Add Skill Form -->
<div class="card" style="margin-bottom: 20px;">
    <h4>Add New Skill</h4>
    <form action="process_add_skill.php" method="POST">
        <textarea 
            name="skill_description" 
            placeholder="Describe the skill you acquired..."
            required
            style="width:100%; padding:10px; height:100px;"></textarea>

        <br><br>
        <button type="submit" class="btn-primary">Add Skill</button>
    </form>
</div>

<!-- Skills Table -->
<table class="styled-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Skill Description</th>
            <th>Date Added</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

    <?php if (count($skills) > 0): ?>
        <?php foreach ($skills as $index => $skill): ?>
            <tr>
                <td><?= $index + 1; ?></td>
                <td><?= htmlspecialchars($skill['skill_description']); ?></td>
                <td><?= $skill['created_at']; ?></td>
                <td>
                    <form action="delete_skill.php" method="POST" 
                          onsubmit="return confirm('Delete this skill?');">
                        <input type="hidden" name="skill_id" 
                               value="<?= $skill['skill_id']; ?>">
                        <button type="submit" class="btn-danger">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4">No skills documented yet.</td>
        </tr>
    <?php endif; ?>

    </tbody>
</table>

</div>

<?php include '../../includes/footer.php'; ?>