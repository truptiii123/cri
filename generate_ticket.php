<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'vendor/autoload.php'; // For TCPDF

use TCPDF;

requireLogin();

if (!isset($_SESSION['last_courier'])) {
    header('Location: add_courier.php');
    exit;
}

$courier = $_SESSION['last_courier'];

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Courier Tracking System');
$pdf->SetAuthor($_SESSION['agent_name']);
$pdf->SetTitle('Courier Ticket - ' . $courier['courier_id']);

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Create ticket content
$html = '
<style>
    .header { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px; }
    .ticket-id { text-align: center; font-size: 16px; margin-bottom: 20px; background-color: #f0f0f0; padding: 10px; }
    .section { margin-bottom: 15px; }
    .label { font-weight: bold; }
    .value { margin-left: 10px; }
    .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #666; }
</style>

<div class="header">COURIER TRACKING TICKET</div>

<div class="ticket-id">
    Courier ID: ' . $courier['courier_id'] . '
</div>

<div class="section">
    <div class="label">Date:</div>
    <div class="value">' . date('d M Y', strtotime($courier['created_date'])) . '</div>
</div>

<div class="section">
    <div class="label">From Party:</div>
    <div class="value">' . htmlspecialchars($courier['from_party_name']) . '</div>
</div>

<div class="section">
    <div class="label">To Party:</div>
    <div class="value">' . htmlspecialchars($courier['to_party_name']) . '</div>
</div>

<div class="section">
    <div class="label">Sender:</div>
    <div class="value">' . htmlspecialchars($courier['sender_name']) . '</div>
</div>

<div class="section">
    <div class="label">Receiver:</div>
    <div class="value">' . htmlspecialchars($courier['receiver_name']) . '</div>
</div>

<div class="section">
    <div class="label">Weight:</div>
    <div class="value">' . $courier['weight'] . ' kg</div>
</div>

<div class="section">
    <div class="label">Amount:</div>
    <div class="value">₹ ' . $courier['amount'] . '</div>
</div>

<div class="section">
    <div class="label">Agent:</div>
    <div class="value">' . htmlspecialchars($_SESSION['agent_name']) . '</div>
</div>

<div class="footer">
    Generated on: ' . date('d M Y, h:i A') . '<br>
    Keep this ticket for reference and tracking purposes.
</div>
';

// Print text using writeHTMLCell()
$pdf->writeHTML($html, true, false, true, false, '');

// Clean any output buffer
ob_clean();

// Close and output PDF document
$pdf->Output('courier_ticket_' . $courier['courier_id'] . '.pdf', 'D');

// Clear the session data
unset($_SESSION['last_courier']);
?>