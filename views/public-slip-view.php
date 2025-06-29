<?php
/**
 * The template for displaying the acknowledgement slip.
 *
 * This template is used by the 'reunion_acknowledgement_slip' shortcode.
 * It handles both searching for a registration and displaying the slip.
 *
 * @package Reunion_Registration
 */

// Don't access this file directly.
if (!defined('ABSPATH')) {
    exit;
}
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<style>
    .acknowledgement-container {
        max-width: 800px;
        margin: 20px auto;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        background: #f0f2f5;
        padding: 20px;
        border-radius: 8px;
    }
    .search-box {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
    }
    .search-box h3 {
        margin-top: 0;
    }
    .search-box form {
        display: flex;
        gap: 10px;
    }
    .search-box input[type="text"] {
        flex-grow: 1;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .search-box button {
        padding: 10px 20px;
        border: none;
        background-color: #0073aa;
        color: white;
        border-radius: 4px;
        cursor: pointer;
    }
    .slip-wrapper {
        border: 1px solid #ddd;
        box-shadow: 0 0 15px rgba(0,0,0,0.07);
        padding: 20px;
        border-radius: 10px;
        background: #fff;
    }
    .slip-header {
        text-align: center;
        padding-bottom: 10px;
        margin-bottom: 15px;
        border-bottom: 2px dashed #ccc;
    }
    .slip-header .logo {
        max-width: 150px;
        max-height: 100px;
        margin-bottom: 15px;
    }
    .slip-profile-pic {
        border: 3px solid #ddd;
        padding: 4px;
        border-radius: 50%;
        width: 120px;
        height: 120px;
        object-fit: cover;
    }
    .slip-details-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .slip-details-table td {
        padding: 12px;
        border: 1px solid #eee;
    }
    .slip-details-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .slip-details-table td:first-child {
        font-weight: bold;
        width: 30%;
    }
    .slip-footer {
        text-align: center;
        margin-top: 30px;
        font-style: italic;
        color: #555;
        border-top: 1px solid #ccc;
        padding-top: 10px;
    }
    .slip-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
        padding: 10px 0;
    }
    .slip-buttons button {
        padding: 12px 25px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        border: none;
        color: #fff;
        transition: background-color 0.2s ease-in-out;
    }
    .print-btn {
        background: #0073aa;
    }
    .print-btn:hover {
        background: #005a87;
    }
    .pdf-btn {
        background: #d63638;
    }
    .pdf-btn:hover {
        background: #b02a2c;
    }
    .pdf-btn:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }
    .no-record {
        background: #f8d7da;
        color: #721c24;
        padding: 15px;
        border: 1px solid #f5c6cb;
        border-radius: 5px;
        text-align: center;
    }
    @media print {
        body, .acknowledgement-container {
            margin: 0;
            padding: 0;
            background: #fff;
        }
        body * {
            visibility: hidden;
        }
        #slip-to-print,
        #slip-to-print * {
            visibility: visible;
        }
        #slip-to-print {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .search-box,
        .slip-buttons,
        .navbar,
        .footer {
            display: none !important;
        }
        .slip-wrapper {
            border: none;
            box-shadow: none;
        }
    }
</style>

<div class="acknowledgement-container">
    <?php if (empty($atts['record'])) : ?>
        <div class="search-box">
            <h3>Download Acknowledgement Slip</h3>
            <form method="get" action="">
                <input type="text" name="search_query" value="<?php echo esc_attr($search_term ?? ''); ?>" placeholder="Enter Unique ID or Mobile Number" required>
                <button type="submit">Search</button>
            </form>
        </div>
    <?php endif; ?>

    <?php if ($record) : ?>
        <div id="slip-to-print">
            <div class="slip-wrapper">
                <div class="slip-header">
                    <?php if ($logo_base64) : ?><img src="<?php echo esc_attr($logo_base64); ?>" alt="Logo" class="logo"><?php endif; ?>
                    <h2>Reunion - Acknowledgement Slip</h2>
                </div>

                <?php if ($record->profile_picture_url) : ?>
                    <div style="text-align:center; margin-bottom:20px;">
                        <img src="<?php echo esc_url($record->profile_picture_url); ?>" alt="Profile Picture" class="slip-profile-pic">
                    </div>
                <?php endif; ?>

                <table class="slip-details-table">
                    <tr>
                        <td>Registration ID</td>
                        <td id="slip-unique-id"><strong><?php echo esc_html($record->unique_id); ?></strong></td>
                    </tr>
                    <tr>
                        <td>Payment Status</td>
                        <td style="font-weight:bold; color:<?php echo ($record->status === 'Paid') ? 'green' : 'red'; ?>;">
                            <?php echo esc_html($record->status); ?>
                        </td>
                    </tr>
                     <tr>
                        <td>Total Fee Paid</td>
                        <td><strong><?php echo esc_html(number_format((float)$record->total_fee, 2)); ?> BDT</strong></td>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td><?php echo esc_html($record->name); ?></td>
                    </tr>
                     <tr>
                        <td>Father's Name</td>
                        <td><?php echo esc_html($record->father_name); ?></td>
                    </tr>
                     <tr>
                        <td>Mother's Name</td>
                        <td><?php echo esc_html($record->mother_name); ?></td>
                    </tr>
                    <tr>
                        <td>Batch</td>
                        <td><?php echo esc_html($record->batch); ?></td>
                    </tr>
                     <tr>
                        <td>Profession</td>
                        <td><?php echo esc_html($record->profession); ?></td>
                    </tr>
                     <tr>
                        <td>Blood Group</td>
                        <td><?php echo esc_html($record->blood_group); ?></td>
                    </tr>
                    <tr>
                        <td>T-Shirt Size</td>
                        <td><?php echo esc_html($record->tshirt_size); ?></td>
                    </tr>
                    <tr>
                        <td>Mobile</td>
                        <td><?php echo esc_html($record->mobile_number); ?></td>
                    </tr>
                    <tr>
                        <td>Spouse Attending</td>
                        <td><?php echo esc_html($record->spouse_status); ?></td>
                    </tr>
                    <?php if ($record->spouse_status === 'Yes' && !empty($record->spouse_name)) : ?>
                        <tr>
                            <td>Spouse Name</td>
                            <td><?php echo esc_html($record->spouse_name); ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php
                    if ($record->child_status === 'Yes' && !empty($record->child_details)) {
                        $children = json_decode($record->child_details, true);
                        if (!empty($children) && is_array($children)) {
                    ?>
                            <tr>
                                <td>Children Details</td>
                                <td>
                                    <?php
                                    foreach ($children as $child) {
                                        $child_name = esc_html($child['name']);
                                        $age_str = '(Age not available)';
                                        if (!empty($child['dob'])) {
                                            try {
                                                $dob = new DateTime($child['dob']);
                                                $today = new DateTime('today');
                                                $age = $dob->diff($today)->y;
                                                $age_str = '(Age: ' . $age . ' years)';
                                            } catch (Exception $e) {
                                                $age_str = '(Invalid Date)';
                                            }
                                        }
                                        echo "{$child_name} {$age_str}<br>";
                                    }
                                    ?>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </table>

                <div class="slip-footer">
                    <p>This is a computer-generated acknowledgement slip and requires no signature.</p>
                </div>
            </div>
        </div>

        <div class="slip-buttons">
            <button class="pdf-btn" id="download-pdf-btn">Download PDF</button>
            <button class="print-btn" onclick="window.print()">Print Slip</button>
        </div>

        <script>
            document.getElementById('download-pdf-btn').addEventListener('click', function() {
                const btn = this;
                btn.innerHTML = 'Generating...';
                btn.disabled = true;

                const { jsPDF } = window.jspdf;
                const slipElement = document.getElementById('slip-to-print');
                const uniqueId = document.getElementById('slip-unique-id').textContent.trim();
                const originalStyle = slipElement.style.cssText;

                // To ensure the canvas captures the full element, we temporarily override its style
                slipElement.style.width = '800px';
                slipElement.style.position = 'absolute';
                slipElement.style.left = '-9999px'; // Move it off-screen
                slipElement.style.top = '0';


                html2canvas(slipElement, {
                    scale: 2, // Higher scale for better quality
                    useCORS: true, // For cross-origin images
                    logging: false, // Set to true for debugging
                    windowWidth: 800 // Ensure canvas renders at this width
                }).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const pdf = new jsPDF({
                        orientation: 'portrait',
                        unit: 'mm',
                        format: 'a4'
                    });

                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
                    
                    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                    pdf.save(`reunion-slip-${uniqueId}.pdf`);

                }).catch(err => {
                    console.error("PDF Generation Error:", err);
                    alert('Could not generate the PDF. Please try again.');
                }).finally(() => {
                    // Restore original styles and button state
                    slipElement.style.cssText = originalStyle;
                    btn.innerHTML = 'Download PDF';
                    btn.disabled = false;
                });
            });
        </script>

    <?php elseif (!empty($search_term)) : ?>
        <div class="no-record">
            No registration was found for the provided Unique ID or Mobile Number. Please check your input and try again.
        </div>
    <?php endif; ?>
</div>
