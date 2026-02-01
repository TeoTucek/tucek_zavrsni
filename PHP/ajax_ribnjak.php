<?php
require_once 'config.php';

header('Content-Type: application/json');

if(isset($_GET['id'])) {
    $ribnjak_id = clean_input($_GET['id']);
    
    $stmt = $pdo->prepare("SELECT * FROM ribnjaci WHERE id_ribnjaci = ?");
    $stmt->execute([$ribnjak_id]);
    $ribnjak = $stmt->fetch();
    
    if($ribnjak) {
        echo json_encode([
            'success' => true,
            'naziv' => $ribnjak['naziv'],
            'lokacija' => $ribnjak['lokacija'],
            'povrsina' => $ribnjak['povrsina'],
            'cijena_po_satu' => $ribnjak['cijena_po_satu'],
            'max_ribolovaca' => $ribnjak['max_ribolovaca'],
            'opis' => $ribnjak['opis']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ribnjak nije pronađen']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Nije proslijeđen ID']);
}
?>