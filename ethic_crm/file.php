<?php
/**
 * update_barcode_values.php
 *
 * For every row in `variant` with an empty/blank barcode_value:
 *   1. Generate a code from v_id using the existing encryptId() function.
 *   2. Check that code doesn't already exist in barcode_value (safety net -
 *      mathematically impossible here since gcd(333667, 900000000000) = 1,
 *      making encryptId() a bijection, but checked anyway as requested).
 *   3. UPDATE the row with that code.
 *
 * Run once from CLI or browser. Safe to re-run - rows that already have a
 * barcode_value are left untouched.
 */

session_start();
include_once("connect.php"); // must expose a mysqli connection as $con

define('BC_OFFSET',     '100000000000');
define('BC_RANGE',      '900000000000');
define('BC_MULTIPLIER', '333667');
define('BC_INVERSE',    '702999997003');

function encryptId($v_id)
{
    $enc = bcmod(bcmul((string)$v_id, BC_MULTIPLIER), BC_RANGE);
    $enc = bcadd($enc, BC_OFFSET);
    return str_pad($enc, 12, "0", STR_PAD_LEFT);
}

function decryptId($code)
{
    $temp = bcmod(bcsub((string)$code, BC_OFFSET), BC_RANGE);
    $dec  = bcmod(bcmul($temp, BC_INVERSE), BC_RANGE);
    return (int)$dec;
}

function barcodeExists(mysqli $con, string $code, int $excludeVid): bool
{
    $stmt = $con->prepare("SELECT 1 FROM variant WHERE barcode_value = ? AND v_id != ? LIMIT 1");
    $stmt->bind_param("si", $code, $excludeVid);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}

// --- Main run ---

$updated = 0;
$skippedAlreadySet = 0;
$conflicts = [];

$result = $con->query("SELECT v_id, barcode_value FROM variant");

if (!$result) {
    die("Failed to fetch variant rows: " . $con->error);
}

$con->begin_transaction();

try {
    while ($row = $result->fetch_assoc()) {
        $vId = (int)$row['v_id'];
        $existingValue = trim($row['barcode_value']);

        // Skip rows that already have a barcode value
        if ($existingValue !== '') {
            $skippedAlreadySet++;
            continue;
        }

        $code = encryptId($vId);

        // Safety check: make sure this generated value isn't already used
        // by a different row before writing it.
        if (barcodeExists($con, $code, $vId)) {
            // Should not happen given encryptId() is a bijection over the
            // configured range, but log it instead of silently overwriting.
            $conflicts[] = ['v_id' => $vId, 'code' => $code];
            continue;
        }

        $stmt = $con->prepare("UPDATE variant SET barcode_value = ? WHERE v_id = ?");
        $stmt->bind_param("si", $code, $vId);
        $stmt->execute();
        $stmt->close();

        // Echo the actual query with values substituted in, for visual log
        echo "UPDATE variant SET barcode_value = '{$code}' WHERE v_id = {$vId};<br>\n";

        $updated++;
    }

    $con->commit();
} catch (Exception $e) {
    $con->rollback();
    die("Error during update, rolled back: " . $e->getMessage());
}

echo "<br>Done.<br>\n";
echo "Updated: $updated<br>\n";
echo "Skipped (already had a value): $skippedAlreadySet<br>\n";
echo "Conflicts (needs manual review): " . count($conflicts) . "<br>\n";

if (!empty($conflicts)) {
    foreach ($conflicts as $c) {
        echo "&nbsp;&nbsp;v_id={$c['v_id']} generated_code={$c['code']} already exists elsewhere<br>\n";
    }
}