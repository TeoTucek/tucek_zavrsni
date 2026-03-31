<?php
// =====================================================
// zauzeti_datumi.php - AJAX dohvat zauzetih datuma
// =====================================================

require_once 'spoji.php';

if (isset($_POST['id_lokacije'])) {
    $id_lokacije = (int)$_POST['id_lokacije'];
    
    $stmt = $mysqli->prepare("SELECT datum_rezervacije FROM rezervacije 
                              WHERE id_lokacije = ? AND status != 'otkazano'");
    $stmt->bind_param("i", $id_lokacije);
    $stmt->execute();
    $rez = $stmt->get_result();
    
    $zauzeti = [];
    while ($row = $rez->fetch_assoc()) {
        $zauzeti[] = $row['datum_rezervacije'];
    }
    
    header('Content-Type: application/json');
    echo json_encode($zauzeti);
} else {
    echo json_encode([]);
}
?>