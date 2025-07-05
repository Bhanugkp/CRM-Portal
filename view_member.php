<?php
include 'db_connect.php';

// Fetch member details based on the provided ID
$memberQuery = $conn->query("
SELECT 
m.id AS member_id, 
CONCAT(m.fname, IF(m.lname IS NOT NULL, CONCAT(' ', m.lname), '')) AS full_name,
m.phone AS phone_number, 
m.email AS email_address, 
m.gender AS gender,
m.marital_status AS marital_status,
m.dob AS dob,
m.locality AS locality,
m.village AS village,
m.urban_body as body_type,
m.bodies_name as bodies_name, 
m.locality AS locality,
m.photo AS photo,

m.state_id, m.district_id, m.organization_id, m.role_id, m.block_id,
b.name AS block_name, 
d.name AS district_name, 
s.name AS state_name, 
lk.name AS loksabha_name,
v.id AS vidhansabha_id,
v.name AS vidhansabha_name,
m.created_at AS join_date, 
r.name AS role_name, 
o.name AS organization_name, 
CONCAT(u.fname, ' ', u.lname) AS referrer_name, 
u.id AS referrer_id,
q.name as qualification
FROM 
`members` AS m 
INNER JOIN 
`users` AS u ON m.ref_by = u.id 
INNER JOIN 
`blocks` AS b ON m.block_id = b.id 
INNER JOIN 
`districts` AS d ON m.district_id = d.id 
INNER JOIN 
`states` AS s ON m.state_id = s.id 
INNER JOIN 
`role` AS r ON m.role_id = r.id 
INNER JOIN 
`organization` AS o ON m.organization_id = o.id 
INNER JOIN 
`vidhansabha` AS v ON v.id = m.vidhansabha_id
INNER JOIN 
`loksabha` AS lk ON lk.id = v.loksabha_id 
INNER JOIN 
`qualification` AS q ON m.education_id = q.id 
WHERE 
m.id = " . $_GET["id"] . "
ORDER BY 
m.id ASC;
")->fetch_assoc();




$memberDetails = $memberQuery;

function generateUniqueId($m) {
    $state_id = str_pad($m['state_id'], 1, '0', STR_PAD_LEFT);
    $district_id = str_pad($m['district_id'], 2, '0', STR_PAD_LEFT);
    $organization_id = str_pad($m['organization_id'], 2, '0', STR_PAD_LEFT);
    $role_id = str_pad($m['role_id'], 2, '0', STR_PAD_LEFT);
    $block_id = str_pad($m['block_id'], 3, '0', STR_PAD_LEFT);
    
    return $state_id . $district_id . $organization_id . $role_id . $block_id;
}


?>


<!-- Enhanced CSS -->
<style>
    .member-card {
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .member-header {
        background: linear-gradient(135deg, #961313 0%, #c21818 100%);
        color: white;
        padding: 20px;
        margin-bottom: 20px;
    }
    .member-photo {
        width: 180px;
        height: 80px;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
    .detail-section {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
    }
    .detail-section:last-child {
        border-bottom: none;
    }
    .detail-label {
        font-weight: 600;
        color: #555;
        min-width: 120px;
        display: inline-block;
    }
    .detail-value {
        color: #333;
    }
    .section-title {
        color: #961313;
        border-bottom: 2px solid #961313;
        padding-bottom: 5px;
        margin-bottom: 15px;
        font-weight: 600;
    }
    .address-box {
        background: #f9f9f9;
        border-left: 4px solid #961313;
        padding: 15px;
        border-radius: 0 5px 5px 0;
    }
    .print-btn {
        background: #961313;
        color: white;
        border: none;
        transition: all 0.3s;
    }
    .print-btn:hover {
        background: #b51a1a;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .hindi-text {
        font-family: 'Noto Sans Devanagari', 'Arial', sans-serif;
    }
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            background: white !important;
            color: black !important;
        }
        .member-card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
</style>

<div class="container-fluid member-card p-0">
    <!-- Header Section -->
    <div class="member-header text-center">
        <div class="row align-items-center">
            <div class="col-md-3">
                <img src="./uploads/members_photos/<?php echo isset($memberDetails['photo'])?$memberDetails['photo']:'default.png'; ?>" 
                    alt="member_image" 
                    class="member-photo rounded-circle">
            </div>
            <div class="col-md-9 text-md-left text-center">
                <h2 class="mb-2"><b><?php echo ucwords($memberDetails['full_name'] ?? 'N/A'); ?></b></h2>
                <h5 class="mb-0"><?php echo $memberDetails['role_name'] ?? 'N/A'; ?></h5>
                <p class="mb-0"><?php echo $memberDetails['organization_name'] ?? 'N/A'; ?></p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-4">
        <div class="row">
            <!-- Personal Details -->
            <div class="col-md-6">
                <div class="detail-section">
                    <h5 class="section-title">Personal Details</h5>
                    <div class="mb-2">
                        <span class="detail-label">Unique ID:</span>
                        <span class="detail-value"><?php echo generateUniqueId($memberDetails); ?></span>
                    </div>
                    <div class="mb-2">
                        <span class="detail-label hindi-text">फ़ोन न०:</span>
                        <span class="detail-value"><?php echo $memberDetails['phone_number'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="mb-2">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value">
                            <a href="mailto:<?php echo $memberDetails['email_address'] ?? '#'; ?>">
                                <?php echo $memberDetails['email_address'] ?? 'N/A'; ?>
                            </a>
                        </span>
                    </div>
                    <div class="mb-2">
                        <span class="detail-label hindi-text">जन्म तिथि:</span>
                        <span class="detail-value"><?php echo isset($memberDetails['dob'])?date('d/m/Y', strtotime($memberDetails['dob'])):'N/A'; ?></span>
                    </div>
                    <div class="mb-2">
                        <span class="detail-label hindi-text">लिंग:</span>
                        <span class="detail-value"><?php echo ($memberDetails['gender'] == 1) ? 'पुरुष' : 'महिला'; ?></span>
                    </div>
                    <div class="mb-2">
                        <span class="detail-label">Qualification:</span>
                        <span class="detail-value"><?php echo $memberDetails['qualification'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="mb-2">
                        <span class="detail-label">Join Date:</span>
                        <span class="detail-value"><?php echo date('d/m/Y', strtotime($memberDetails['join_date'])) ?? 'N/A'; ?></span>
                    </div>
                </div>
            </div>

            <!-- Address Details -->
            <div class="col-md-6">
                <div class="detail-section">
                    <h5 class="section-title">Address Details</h5>
                    <div class="address-box">
                        <?php if ($memberDetails['locality'] == 2): ?>
                            <div class="mb-2">
                                <span class="detail-label hindi-text">ग्राम:</span>
                                <span class="detail-value"><?php echo $memberDetails['village'] ?? 'N/A'; ?></span>
                            </div>
                        <?php else: ?>
                            <div class="mb-2">
                                <span class="detail-label hindi-text">
                                    <?php 
                                    switch ($memberDetails['body_type']) {
                                        case 1: echo 'नगर निगम'; break;
                                        case 2: echo 'नगर पालिका'; break;
                                        default: echo 'नगर पंचायत';
                                    }
                                    ?>:
                                </span>
                                <span class="detail-value"><?php echo $memberDetails['bodies_name'] ?? 'N/A'; ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-2">
                            <span class="detail-label hindi-text">ब्लाक:</span>
                            <span class="detail-value"><?php echo $memberDetails['block_name'] ?? 'N/A'; ?></span>
                        </div>
                        <div class="mb-2">
                            <span class="detail-label hindi-text">जिला:</span>
                            <span class="detail-value"><?php echo $memberDetails['district_name'] ?? 'N/A'; ?></span>
                        </div>
                        <div class="mb-2">
                            <span class="detail-label hindi-text">राज्य:</span>
                            <span class="detail-value"><?php echo $memberDetails['state_name'] ?? 'N/A'; ?></span>
                        </div>
                        <div class="mb-2">
                            <span class="detail-label">Loksabha:</span>
                            <span class="detail-value"><?php echo $memberDetails['loksabha_name'] ?? 'N/A'; ?></span>
                        </div>
                        <div class="mb-2">
                            <span class="detail-label">Vidhansabha:</span>
                            <span class="detail-value">
                                <?php echo ($memberDetails['vidhansabha_id'] ?? 'N/A') . ' - ' . ($memberDetails['vidhansabha_name'] ?? 'N/A'); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Referrer Details -->
        <div class="row">
            <div class="col-12">
                <div class="detail-section">
                    <h5 class="section-title">Referrer Details</h5>
                    <?php 
                    $referrerQuery = $conn->query("SELECT 
    m.id AS referrer_id, 
    CONCAT(m.fname, ' ', m.lname) AS referrer_name, 
    r.name AS referrer_role, 
    o.name AS referrer_organization 
FROM 
    `users` AS m 
INNER JOIN 
    `role` AS r ON m.role_id = r.id 
INNER JOIN 
    `organization` AS o ON m.organization_id = o.id 
WHERE 
    m.id = " . ($memberDetails['referrer_id'] ?? '0')
  );
                    $referrerDetails = $referrerQuery->fetch_assoc();
                    ?>
                    <div class="alert alert-light">
                        <?php if(isset($referrerDetails['referrer_name'])): ?>
                            <strong><?php echo $referrerDetails['referrer_name']; ?></strong><br>
                            <?php echo $referrerDetails['referrer_role']; ?> - 
                            <?php echo $referrerDetails['referrer_organization']; ?>
                        <?php else: ?>
                            <span class="text-muted">No referrer information available</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer with Print Button -->
    <div class="modal-footer display p-0 m-0 no-print">
        <?php if(isset($memberDetails['full_name']) && isset($memberDetails['phone_number']) 
        && isset($memberDetails['district_name']) && isset($memberDetails['block_name']) 
        && isset($memberDetails['role_name']) && isset($memberDetails['organization_name'])
        ): ?>
            <div class="col-xs-12">
                <button onclick="printCard()" class="btn print-btn">
                    <i class="fa fa-print"></i> Print ID Card
                </button>
            </div>
        <?php endif; ?>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Close
        </button>
    </div>
</div>

<!-- Print Function -->
<script>
function printCard() {
    window.print();
}
</script>

<!-- For No JavaScript -->
<noscript>
    <style>
        .member-card {
            border: 1px solid #ddd;
        }
        .detail-label {
            font-weight: bold;
        }
    </style>
    <h3 class="text-center"><b>Member Details</b></h3>
</noscript>

<?php 
$marr['full_name'] = $memberDetails['full_name'];
$marr['phone'] = $memberDetails['phone_number'];
$marr['role'] = $memberDetails['role_name'];
$marr['unique_id'] = generateUniqueId($memberDetails);

?>
<div id="idCard" style="display: none">
    <div style="width: 3.8in">
        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
            viewBox="0 0 1013 638" style="enable-background:new 0 0 1013 638;" xml:space="preserve">
            <style type="text/css">
                @import url('https://fonts.googleapis.com/css2?family=Noto+Serif+Devanagari:wght@100..900&display=swap');
                .st0{clip-path:url(#SVGID_00000036241441239143324900000014980726716962306446_);}
                .st1{fill:#961313;}
                .st2{fill:#EFEFEF;}
                .st3{fill:#FFFFFF;}
                .st4{font-family:'NirmalaUI';}
                .st5{font-size:36px;}
                .st6{font-family:'ArialMT';}
                .st7{font-size:12px;}
                .st8{letter-spacing:1;}
                .st9{letter-spacing:18;}
                .st10{letter-spacing:11;}
                .st11{font-size:18px;}
                .st12{font-family:'Arial-Black';}
                .st13{font-size:72px; font-weight:900}
                .st14{letter-spacing:24;}
                .mfont { font-family: "Noto Serif Devanagari", serif;}
            </style>
            <g>
                <g>
                    <g>
                        <defs>
                            <rect id="SVGID_1_" width="1013" height="638"/>
                        </defs>
                        <clipPath id="SVGID_00000116918328964866509600000002770320916561605520_">
                            <use xlink:href="#SVGID_1_"  style="overflow:visible;"/>
                        </clipPath>
                        <g style="clip-path:url(#SVGID_00000116918328964866509600000002770320916561605520_);">
                            <path class="st1" d="M40,0h933c22.1,0,40,17.9,40,40v558c0,22.1-17.9,40-40,40H40c-22.1,0-40-17.9-40-40V40C0,17.9,17.9,0,40,0z
                                "/>
                            <path class="st2" d="M0,40C0,17.9,17.9,0,40,0h933c22.1,0,40,17.9,40,40c0,34.6,16.6,88.7,0,122.6c-125.4,256.1-1013,0-1013,0
                                V40z"/>
                            <path class="st1" d="M319.4,102h-12.5l-16.7-25.2V102h-12.5V56.9h12.5l16.7,25.5V56.9h12.5V102z M339.1,56.9V102h-12.5V56.9
                                H339.1z M363.2,102.4c-5.2,0-9.6-1.2-12.9-3.7c-3.4-2.5-5.2-6.1-5.4-10.8h13.4c0.1,1.6,0.6,2.7,1.4,3.5c0.8,0.7,1.9,1.1,3.1,1.1
                                s2.1-0.3,2.8-0.8c0.8-0.6,1.2-1.4,1.2-2.4c0-1.3-0.6-2.3-1.9-3.1c-1.2-0.7-3.2-1.5-6-2.4c-2.9-1-5.3-1.9-7.2-2.8
                                c-1.8-0.9-3.4-2.3-4.7-4c-1.3-1.8-2-4.1-2-7s0.7-5.4,2.2-7.4c1.5-2.1,3.5-3.7,6-4.7s5.5-1.6,8.7-1.6c5.2,0,9.4,1.2,12.5,3.7
                                c3.2,2.4,4.8,5.9,5.1,10.3h-13.6c0-1.4-0.5-2.4-1.3-3.1s-1.8-1-3-1c-0.9,0-1.7,0.3-2.3,0.8c-0.6,0.6-0.9,1.3-0.9,2.4
                                c0,0.9,0.3,1.6,1,2.2s1.5,1.1,2.5,1.6c1,0.4,2.4,1,4.4,1.7c2.9,1,5.2,2,7,2.9c1.9,0.9,3.5,2.3,4.8,4c1.4,1.7,2,3.9,2,6.5
                                c0,2.7-0.7,5.1-2,7.2s-3.3,3.8-5.8,5.1C369.7,101.8,366.7,102.4,363.2,102.4z M426.3,56.9V102h-12.5V83.9h-15.3V102H386V56.9
                                h12.5v17h15.3v-17H426.3z M462,94.6h-16l-2.4,7.4h-13.2l16.4-45.1h14.5l16.4,45.1h-13.2L462,94.6z M458.9,85L454,70.4L449.2,85
                                H458.9z M499.6,56.9c4.7,0,8.9,1,12.4,2.9c3.6,1.9,6.3,4.5,8.3,7.9s2.9,7.3,2.9,11.7c0,4.4-1,8.2-2.9,11.6s-4.7,6.1-8.3,8.1
                                c-3.5,1.9-7.7,2.9-12.4,2.9h-17.9V56.9H499.6z M498.7,90.9c3.7,0,6.5-1,8.6-3s3.1-4.8,3.1-8.5s-1-6.6-3.1-8.6s-5-3.1-8.6-3.1
                                h-4.4v23.2C494.3,90.9,498.7,90.9,498.7,90.9z M576.7,71.9c0,2.7-0.6,5.2-1.9,7.5c-1.2,2.2-3.1,4-5.7,5.4c-2.5,1.3-5.6,2-9.3,2
                                h-6.2V102h-12.5V56.9h18.8c3.6,0,6.7,0.6,9.2,1.9c2.6,1.3,4.5,3.1,5.8,5.3C576.1,66.4,576.7,69,576.7,71.9z M558.6,76.8
                                c3.5,0,5.3-1.6,5.3-4.9s-1.8-4.9-5.3-4.9h-5v9.8H558.6z M610.3,94.6h-16l-2.4,7.4h-13.2l16.4-45.1h14.5L626,102h-13.2
                                L610.3,94.6z M607.2,85l-4.9-14.6L597.5,85H607.2z M652.9,102l-9-16.6h-1.3V102h-12.5V56.9h19.8c3.6,0,6.7,0.6,9.2,1.9
                                c2.5,1.2,4.4,3,5.7,5.2s1.9,4.6,1.9,7.4c0,3.1-0.9,5.8-2.6,8.1c-1.7,2.3-4.1,3.9-7.4,4.9l10.2,17.6H652.9z M642.6,76.8h6.2
                                c1.7,0,3-0.4,3.8-1.2c0.9-0.8,1.3-2,1.3-3.5s-0.4-2.6-1.3-3.4c-0.9-0.9-2.1-1.3-3.8-1.3h-6.2V76.8z M706.4,56.9v10h-12V102
                                h-12.5V66.9H670v-10H706.4z M752,56.9l-15.9,30.8V102h-12.6V87.7l-15.9-30.8H722l7.9,17.4l7.9-17.4
                                C737.8,56.9,752,56.9,752,56.9z"/>
                            <path class="st1" d="M290,147h-3.4l-7.6-11.5V147h-3.4v-16.8h3.4l7.6,11.5v-11.5h3.4V147z M296.7,130.2V147h-3.4v-16.8H296.7z
                                M308.6,147l-3.7-6.5h-1.6v6.5H300v-16.8h6.3c1.3,0,2.4,0.2,3.3,0.7c0.9,0.4,1.6,1.1,2,1.8c0.5,0.8,0.7,1.6,0.7,2.6
                                c0,1.1-0.3,2.1-1,3c-0.6,0.9-1.6,1.5-2.9,1.8l4,6.8L308.6,147L308.6,147z M303.3,138h2.8c0.9,0,1.6-0.2,2-0.6
                                c0.4-0.4,0.7-1.1,0.7-1.8c0-0.8-0.2-1.4-0.7-1.8c-0.4-0.4-1.1-0.6-2-0.6h-2.8V138z M324.9,138.4c0.9,0.2,1.7,0.6,2.3,1.4
                                c0.6,0.8,0.9,1.6,0.9,2.6c0,0.9-0.2,1.7-0.7,2.4c-0.4,0.7-1.1,1.2-1.9,1.6s-1.8,0.6-3,0.6h-7.2v-16.8h6.9c1.1,0,2.1,0.2,2.9,0.6
                                c0.8,0.4,1.5,0.9,1.9,1.5c0.4,0.7,0.6,1.4,0.6,2.2c0,1-0.3,1.8-0.8,2.4C326.4,137.7,325.7,138.1,324.9,138.4z M318.7,137.2h3.1
                                c0.8,0,1.4-0.2,1.8-0.5c0.4-0.4,0.6-0.9,0.6-1.6c0-0.7-0.2-1.2-0.6-1.6c-0.4-0.4-1-0.6-1.8-0.6h-3.1V137.2z M322.1,144.3
                                c0.8,0,1.4-0.2,1.9-0.6c0.5-0.4,0.7-0.9,0.7-1.6c0-0.7-0.2-1.3-0.7-1.7s-1.1-0.6-1.9-0.6h-3.3v4.5L322.1,144.3L322.1,144.3z
                                M341.1,143.8h-6.7l-1.1,3.2h-3.5l6-16.8h3.9l6,16.8h-3.6L341.1,143.8z M340.1,141.1l-2.4-7l-2.4,7H340.1z M351.3,144.3h5.5v2.7
                                H348v-16.8h3.4v14.1H351.3z M368.1,130.2V147h-3.4v-16.8H368.1z M385.7,147h-3.4l-7.6-11.5V147h-3.4v-16.8h3.4l7.6,11.5v-11.5
                                h3.4V147z M394.9,130.2c1.8,0,3.3,0.3,4.6,1c1.3,0.7,2.4,1.7,3.1,3s1.1,2.7,1.1,4.4s-0.4,3.2-1.1,4.4s-1.8,2.2-3.1,2.9
                                c-1.3,0.7-2.9,1-4.6,1H389v-16.8h5.9V130.2z M394.8,144.1c1.8,0,3.1-0.5,4.1-1.4c1-1,1.4-2.3,1.4-4.1c0-1.7-0.5-3.1-1.4-4.1
                                c-1-1-2.3-1.5-4.1-1.5h-2.4v11.1H394.8z M409.6,130.2V147h-3.4v-16.8H409.6z M423.2,143.8h-6.7l-1.1,3.2h-3.5l6-16.8h3.9l6,16.8
                                h-3.6L423.2,143.8z M422.3,141.1l-2.4-7l-2.4,7H422.3z M441.6,147.2c-1.2,0-2.2-0.2-3.2-0.6c-0.9-0.4-1.7-1-2.2-1.7
                                s-0.8-1.6-0.8-2.7h3.6c0,0.7,0.3,1.2,0.7,1.6c0.4,0.4,1.1,0.6,1.8,0.6c0.8,0,1.4-0.2,1.8-0.6c0.4-0.4,0.7-0.9,0.7-1.5
                                c0-0.5-0.2-0.9-0.5-1.2s-0.7-0.6-1.2-0.7c-0.4-0.2-1.1-0.4-1.9-0.6c-1.1-0.3-2-0.6-2.7-0.9c-0.7-0.3-1.3-0.8-1.8-1.4
                                c-0.5-0.6-0.7-1.5-0.7-2.5s0.2-1.9,0.7-2.6c0.5-0.7,1.2-1.3,2.1-1.7s1.9-0.6,3.1-0.6c1.7,0,3.1,0.4,4.2,1.3
                                c1.1,0.8,1.7,2,1.8,3.5h-3.7c0-0.6-0.3-1-0.7-1.4c-0.4-0.4-1-0.6-1.8-0.6c-0.7,0-1.2,0.2-1.6,0.5s-0.6,0.8-0.6,1.5
                                c0,0.4,0.1,0.8,0.4,1.1s0.7,0.5,1.1,0.7s1.1,0.4,1.9,0.6c1.1,0.3,2,0.6,2.7,1c0.7,0.3,1.3,0.8,1.8,1.4s0.7,1.5,0.7,2.5
                                c0,0.9-0.2,1.7-0.7,2.5s-1.1,1.4-2,1.8C443.9,146.9,442.8,147.2,441.6,147.2z M458.2,147.2c-1.6,0-3-0.4-4.3-1.1
                                c-1.3-0.7-2.4-1.8-3.1-3c-0.8-1.3-1.2-2.8-1.2-4.4c0-1.6,0.4-3.1,1.2-4.4c0.8-1.3,1.8-2.3,3.1-3.1c1.3-0.7,2.8-1.1,4.3-1.1
                                c1.6,0,3,0.4,4.3,1.1c1.3,0.7,2.3,1.8,3.1,3.1c0.8,1.3,1.2,2.8,1.2,4.4c0,1.6-0.4,3.1-1.2,4.4c-0.8,1.3-1.8,2.3-3.1,3
                                C461.2,146.8,459.8,147.2,458.2,147.2z M458.2,144.2c1,0,1.9-0.2,2.7-0.7c0.8-0.5,1.4-1.1,1.8-2s0.6-1.8,0.6-3
                                c0-1.1-0.2-2.1-0.6-2.9c-0.4-0.8-1-1.5-1.8-1.9s-1.7-0.7-2.7-0.7s-1.9,0.2-2.7,0.7c-0.8,0.4-1.4,1.1-1.8,1.9
                                c-0.4,0.8-0.6,1.8-0.6,2.9c0,1.1,0.2,2.1,0.6,3c0.4,0.8,1,1.5,1.8,2C456.3,143.9,457.2,144.2,458.2,144.2z M475.1,147.2
                                c-1.2,0-2.2-0.2-3.2-0.6c-0.9-0.4-1.7-1-2.2-1.7s-0.8-1.6-0.8-2.7h3.6c0,0.7,0.3,1.2,0.7,1.6c0.4,0.4,1.1,0.6,1.8,0.6
                                c0.8,0,1.4-0.2,1.8-0.6c0.4-0.4,0.7-0.9,0.7-1.5c0-0.5-0.2-0.9-0.5-1.2s-0.7-0.6-1.2-0.7c-0.4-0.2-1.1-0.4-1.9-0.6
                                c-1.1-0.3-2-0.6-2.7-0.9c-0.7-0.3-1.3-0.8-1.8-1.4c-0.5-0.6-0.7-1.5-0.7-2.5s0.2-1.9,0.7-2.6c0.5-0.7,1.2-1.3,2.1-1.7
                                s1.9-0.6,3.1-0.6c1.7,0,3.1,0.4,4.2,1.3c1.1,0.8,1.7,2,1.8,3.5h-3.7c0-0.6-0.3-1-0.7-1.4c-0.4-0.4-1-0.6-1.8-0.6
                                c-0.7,0-1.2,0.2-1.6,0.5s-0.6,0.8-0.6,1.5c0,0.4,0.1,0.8,0.4,1.1s0.7,0.5,1.1,0.7s1.1,0.4,1.9,0.6c1.1,0.3,2,0.6,2.7,1
                                c0.7,0.3,1.3,0.8,1.8,1.4s0.7,1.5,0.7,2.5c0,0.9-0.2,1.7-0.7,2.5s-1.1,1.4-2,1.8C477.4,146.9,476.3,147.2,475.1,147.2z
                                M497.8,130.2V147h-3.4v-7.1h-7.2v7.1h-3.4v-16.8h3.4v6.9h7.2v-6.9H497.8z M504.5,130.2V147h-3.4v-16.8H504.5z M519.2,130.2v2.7
                                h-4.5v14h-3.4v-14h-4.5v-2.7H519.2z M541.2,130.2V147h-3.4v-7.1h-7.2v7.1h-3.4v-16.8h3.4v6.9h7.2v-6.9H541.2z M554.8,143.8h-6.7
                                L547,147h-3.5l6-16.8h3.9l6,16.8h-3.6L554.8,143.8z M553.9,141.1l-2.4-7l-2.4,7H553.9z M580,130.2V147h-3.4v-10.9l-4.5,10.9
                                h-2.5l-4.5-10.9V147h-3.4v-16.8h3.8l5.3,12.5l5.3-12.5H580z M593.6,143.8h-6.7l-1.1,3.2h-3.5l6-16.8h3.9l6,16.8h-3.6
                                L593.6,143.8z M592.7,141.1l-2.4-7l-2.4,7H592.7z M609.1,147l-3.7-6.5h-1.6v6.5h-3.4v-16.8h6.3c1.3,0,2.4,0.2,3.3,0.7
                                c0.9,0.4,1.6,1.1,2,1.8c0.5,0.8,0.7,1.6,0.7,2.6c0,1.1-0.3,2.1-1,3c-0.6,0.9-1.6,1.5-2.9,1.8l4,6.8L609.1,147L609.1,147z
                                M603.9,138h2.8c0.9,0,1.6-0.2,2-0.6c0.4-0.4,0.7-1.1,0.7-1.8c0-0.8-0.2-1.4-0.7-1.8c-0.4-0.4-1.1-0.6-2-0.6h-2.8V138z
                                M626.1,143.8h-6.7l-1.1,3.2h-3.5l6-16.8h3.9l6,16.8h-3.6L626.1,143.8z M625.2,141.1l-2.4-7l-2.4,7H625.2z M649,143.8h-6.7
                                l-1.1,3.2h-3.5l6-16.8h3.9l6,16.8H650L649,143.8z M648.1,141.1l-2.4-7l-2.4,7H648.1z M666.2,143.8h-6.7l-1.1,3.2h-3.5l6-16.8
                                h3.9l6,16.8h-3.6L666.2,143.8z M665.3,141.1l-2.4-7l-2.4,7H665.3z M691.4,130.2V147H688v-10.9l-4.5,10.9H681l-4.5-10.9V147h-3.4
                                v-16.8h3.8l5.3,12.5l5.3-12.5H691.4z M706.3,130.2c1.8,0,3.3,0.3,4.6,1s2.4,1.7,3.1,3c0.7,1.3,1.1,2.7,1.1,4.4s-0.4,3.2-1.1,4.4
                                c-0.7,1.2-1.8,2.2-3.1,2.9s-2.9,1-4.6,1h-5.9v-16.8h5.9V130.2z M706.2,144.1c1.8,0,3.1-0.5,4.1-1.4c1-1,1.4-2.3,1.4-4.1
                                c0-1.7-0.5-3.1-1.4-4.1c-1-1-2.3-1.5-4.1-1.5h-2.4v11.1H706.2z M727.9,143.8h-6.7l-1.1,3.2h-3.5l6-16.8h3.9l6,16.8H729
                                L727.9,143.8z M727,141.1l-2.4-7l-2.4,7H727z M738.2,144.3h5.5v2.7h-8.9v-16.8h3.4V144.3z"/>
                            <path d="M415.8,193.5V216h-5.5v-13.5l-5,13.5h-4.4l-5.1-13.5V216h-5.5v-22.5h6.5l6.3,15.6l6.2-15.6H415.8z M425.2,197.9v4.5h7.3
                                v4.2h-7.3v4.9h8.3v4.4h-13.8v-22.5h13.8v4.4h-8.3V197.9z M462.5,193.5V216H457v-13.5l-5,13.5h-4.4l-5.1-13.5V216H437v-22.5h6.5
                                l6.3,15.6l6.2-15.6H462.5z M480,204.5c1.3,0.3,2.3,0.9,3.1,2c0.8,1,1.2,2.2,1.2,3.5c0,1.9-0.7,3.4-2,4.5s-3.1,1.6-5.5,1.6h-10.5
                                v-22.5h10.1c2.3,0,4.1,0.5,5.3,1.6c1.3,1,2,2.5,2,4.3c0,1.3-0.4,2.4-1.1,3.3C482.1,203.5,481.2,204.1,480,204.5z M471.9,202.6
                                h3.6c0.9,0,1.6-0.2,2-0.6c0.5-0.4,0.7-1,0.7-1.8s-0.2-1.4-0.7-1.8c-0.5-0.4-1.2-0.6-2-0.6h-3.6V202.6z M475.9,211.6
                                c0.9,0,1.6-0.2,2.1-0.6c0.5-0.4,0.8-1,0.8-1.8s-0.3-1.4-0.8-1.9c-0.5-0.4-1.2-0.7-2.1-0.7h-4v5H475.9z M493,197.9v4.5h7.3v4.2
                                H493v4.9h8.3v4.4h-13.8v-22.5h13.8v4.4H493V197.9z M516.3,216l-4.7-8.5h-1.3v8.5h-5.5v-22.5h9.2c1.8,0,3.3,0.3,4.5,0.9
                                c1.3,0.6,2.2,1.5,2.8,2.6s0.9,2.3,0.9,3.6c0,1.5-0.4,2.8-1.3,4c-0.8,1.2-2.1,2-3.7,2.5l5.2,8.9H516.3z M510.3,203.6h3.4
                                c1,0,1.8-0.2,2.2-0.7c0.5-0.5,0.8-1.2,0.8-2.1c0-0.9-0.3-1.5-0.8-2s-1.2-0.7-2.2-0.7h-3.4L510.3,203.6L510.3,203.6z
                                M531.6,204.7c0-2.2,0.5-4.2,1.4-5.9c1-1.7,2.3-3.1,4-4.1s3.7-1.5,5.9-1.5c2.7,0,4.9,0.7,6.8,2.1c1.9,1.4,3.2,3.3,3.8,5.8h-6
                                c-0.4-0.9-1.1-1.7-1.9-2.1c-0.8-0.5-1.7-0.7-2.8-0.7c-1.7,0-3.1,0.6-4.1,1.8s-1.6,2.7-1.6,4.7s0.5,3.5,1.6,4.7
                                c1,1.2,2.4,1.8,4.1,1.8c1,0,2-0.2,2.8-0.7s1.5-1.2,1.9-2.1h6c-0.6,2.4-1.9,4.4-3.8,5.8c-1.9,1.4-4.2,2.1-6.8,2.1
                                c-2.2,0-4.1-0.5-5.9-1.4c-1.7-1-3-2.3-4-4.1C532.1,208.9,531.6,207,531.6,204.7z M570.9,212h-8.4l-1.3,4h-5.7l8.1-22.5h6.3
                                L578,216h-5.8L570.9,212z M569.5,207.8l-2.8-8.2l-2.8,8.2H569.5z M591.9,216l-4.7-8.5h-1.3v8.5h-5.5v-22.5h9.2
                                c1.8,0,3.3,0.3,4.5,0.9c1.3,0.6,2.2,1.5,2.8,2.6s0.9,2.3,0.9,3.6c0,1.5-0.4,2.8-1.3,4c-0.8,1.2-2.1,2-3.7,2.5l5.2,8.9H591.9z
                                M585.9,203.6h3.4c1,0,1.8-0.2,2.2-0.7c0.5-0.5,0.8-1.2,0.8-2.1c0-0.9-0.3-1.5-0.8-2s-1.2-0.7-2.2-0.7h-3.4V203.6z M609.8,193.5
                                c2.4,0,4.4,0.5,6.2,1.4s3.1,2.3,4.1,4s1.5,3.6,1.5,5.9c0,2.2-0.5,4.1-1.5,5.9c-1,1.7-2.3,3-4.1,4c-1.8,0.9-3.8,1.4-6.2,1.4h-8.4
                                v-22.5L609.8,193.5L609.8,193.5z M609.4,211.3c2.1,0,3.7-0.6,4.8-1.7c1.2-1.1,1.7-2.7,1.7-4.8s-0.6-3.7-1.7-4.8
                                c-1.2-1.2-2.8-1.7-4.8-1.7h-2.6v13.1h2.6V211.3z"/>
                            <path d="M388.4,219.2h29.4v1.6h-29.4V219.2z M417.8,219.2h46.7v1.6h-46.7V219.2z M464.5,219.2h66.1v1.6h-66.1L464.5,219.2
                                L464.5,219.2z M530.5,219.2h24.4v1.6h-24.4V219.2z M554.9,219.2h67.7v1.6h-67.7V219.2z"/>
                        </g>
                    </g>
                </g>
            </g>
            <image style="overflow:visible;enable-background:new;" width="259" height="262" xlink:href="assets/img/logo.png"  transform="matrix(0.6918 0 0 0.6918 55.3329 9.366)">
            </image>
            <text transform="matrix(1 0 0 1 232.4296 357.885)" class="st3 st4 st5 mfont">नाम</text>
            <text transform="matrix(1 0 0 1 340.4296 357.885)" class="st3 st6 st5">:</text>
            <text transform="matrix(1 0 0 1 376.4296 362.0204)" class="st3 st4 st5 mfont"><?php echo ucwords($memberDetails['full_name']); ?></text>
            <text transform="matrix(1 0 0 1 232.4296 408.885)" class="st3 st4 st5 mfont">फ़ोन</text>
            <text transform="matrix(1 0 0 1 340.4296 405.885)" class="st3 st6 st5">:</text>
            <text transform="matrix(1 0 0 1 376.4296 408.885)" class="st3 st6 st5">+91-<?php echo $memberDetails['phone_number']; ?></text>
            <text transform="matrix(1 0 0 1 243.4805 460.3974)" class="st3 st4 st5 mfont">पद</text>
            <text transform="matrix(1 0 0 1 341.8066 455.4106)" class="st3 st6 st5">: </text>
            <text transform="matrix(1 0 0 1 381.7435 462.353)" class="st3 st4 st5 mfont"><?php echo $memberDetails['role_name']; ?></text>
            <text transform="matrix(4.489659e-11 -1 1 4.489659e-11 1000.082 549.7266)" class="st3 st6 st11">JOIN DATE: <?php echo $memberDetails['join_date']; ?></text>
            <image style="overflow:visible;enable-background:new;" width="659" height="824" id="image" xlink:href="uploads/members_photos/<?php echo $memberDetails['photo']; ?>"  transform="matrix(0.2656 0 0 0.2656 31.9149 322.4602)" 
           preserveAspectRatio="xMidYMid slice">
            </image>
            <text transform="matrix(1 0 0 1 211.5107 598.4897)" class="st3 st12 st13 st14"><?php echo generateUniqueId($memberDetails); ?></text>
            <image style="overflow:visible;" width="290" height="290" xlink:href='https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=https://crm.nishadparty.com/member_valid?unique_id=<?php echo generateUniqueId($memberDetails);?>&color=ffffff&bgcolor=961313'  transform="matrix(0.6109 0 0 0.6532 739.5319 335)">
            </image>
        </svg>

    </div>

</div>

<script>
function printCard() {
    const printContent = document.getElementById('idCard').innerHTML;
    const printWindow = window.open('', '');

    printWindow.document.write('<html><head><title>ID Card</title></head><body>');
    printWindow.document.write(printContent);
    printWindow.document.write('</body></html>');
    printWindow.document.close();

    printWindow.onload = function() {
        printWindow.focus(); // Make sure the print window is focused
        printWindow.print(); // Trigger the print dialog
    };
}

</script>
