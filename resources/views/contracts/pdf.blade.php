<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สัญญาเช่าหอพัก - {{ $contract->contract_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Thai', sans-serif;
            font-size: 13px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .mt-1 { margin-top: 5px; }
        .mt-2 { margin-top: 10px; }
        .mt-3 { margin-top: 15px; }
        .mt-4 { margin-top: 20px; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-4 { margin-bottom: 20px; }
        .underline { border-bottom: 1px solid #333; display: inline-block; min-width: 200px; }
        .underline-sm { border-bottom: 1px solid #333; display: inline-block; min-width: 80px; }
        .underline-md { border-bottom: 1px solid #333; display: inline-block; min-width: 120px; }
        .underline-lg { border-bottom: 1px solid #333; display: inline-block; min-width: 250px; }
        .bold { font-weight: bold; }
        .indent { margin-left: 30px; }
        
        .contract-title {
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .article {
            margin-top: 12px;
        }
        
        .article-number {
            font-weight: bold;
        }
        
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
        }
        
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="text-center">
        <h1 class="contract-title">สัญญาเช่าหอพัก</h1>
        <p>เลขที่ {{ $contract->contract_number }}</p>
    </div>

    <div class="mt-4">
        <p>สัญญาเช่าหอพักฉบับนี้ทำขึ้น ณ <span class="underline-lg">{{ $contract->landlord_address ?? '........................................................' }}</span></p>
        <p>เมื่อวันที่ <span class="underline-sm">{{ $contract->contract_date ? \Carbon\Carbon::parse($contract->contract_date)->format('d') : '......' }}</span> 
        เดือน <span class="underline-md">{{ $contract->contract_date ? \Carbon\Carbon::parse($contract->contract_date)->format('m') : '............' }}</span> 
        พ.ศ. <span class="underline-sm">{{ $contract->contract_date ? \Carbon\Carbon::parse($contract->contract_date)->year + 543 : '............' }}</span></p>
    </div>

    <div class="mt-4">
        <p><span class="bold">ระหว่าง</span></p>
        <p>ผู้ให้เช่า: <span class="underline-lg">{{ $contract->landlord_name ?? '........................................................' }}</span></p>
        <p>ที่อยู่: <span class="underline-lg">{{ $contract->landlord_address ?? '................................................................................................................................' }}</span></p>
        <p>เลขทะเบียนนิติบุคคล/บัตรประจำตัว: <span class="underline-lg">{{ $contract->landlord_id_number ?? '........................................................' }}</span></p>
        <p>โทรศัพท์: <span class="underline-sm">{{ $contract->landlord_phone ?? '........................' }}</span></p>
        <p>ซึ่งต่อไปในสัญญาเช่าหอพักนี้จะเรียกว่า <span class="bold">"ผู้ให้เช่า"</span> ฝ่ายหนึ่ง</p>
    </div>

    <div class="mt-4">
        <p>กับ</p>
        <p>ผู้เช่า: <span class="underline-lg">{{ $guest->first_name ?? '' }} {{ $guest->last_name ?? '' }}</span></p>
        <p>เลขบัตรประจำตัวประชาชน/เลขหนังสือเดินทาง: <span class="underline-lg">{{ $guest->id_number ?? '........................................................' }}</span></p>
        <p>อายุ: <span class="underline-sm">{{ $guest->age ?? '....' }}</span> ปี</p>
        <p>ที่อยู่: <span class="underline-lg">{{ $guest->address ?? '................................................................................................................................' }}</span></p>
        <p>โทรศัพท์: <span class="underline-sm">{{ $guest->phone ?? '........................' }}</span></p>
        <p>ซึ่งต่อไปในสัญญานี้จะเรียกว่า <span class="bold">"ผู้เช่า"</span> ฝ่ายหนึ่ง คู่สัญญาทั้งสองฝ่ายได้ตกลงกันดังนี้</p>
    </div>

    <div class="article">
        <p><span class="article-number">ข้อ 1.</span> ผู้ให้เช่าตกลงให้เช่าและผู้เช่าตกลงเช่าห้องพักเลขที่ <span class="underline">{{ $room->room_number ?? '........' }}</span> ชั้นที่ <span class="underline-sm">{{ $room->floor ?? '...' }}</span> ในอาคารของผู้ให้เช่าเพื่อเป็นที่อยู่อาศัย ภายใต้ระเบียบที่ผู้ให้เช่ากำหนด โดยเฉพาะไม่ก่อกวน ต้องสงบในยามวิาล ไม่ทะเลาะวิวาท ไม่ทำผิดศีลธรรมและกฎหมาย</p>
    </div>

    <div class="article">
        <p><span class="article-number">ข้อ 2.</span> ผู้เช่าตกลงชำระค่าเช่าให้แก่ผู้ให้เช่าล่วงหน้า <span class="underline-sm">{{ $contract->advance_payment_months ?? '......' }}</span> เดือน และเป็นรายเดือน โดยกำหนดชำระค่าเช่าภายในวันที่ <span class="underline-sm">{{ $contract->due_date_day ?? '......' }}</span> ของทุกเดือน ในอัตราค่าเช่าเดือนละ <span class="underline-md">{{ number_format($contract->monthly_rent ?? 0) }}</span> บาท (<span class="underline-lg">{{ $contract->monthly_rent_text ?? '........................................................' }}</span>)</p>
        <p class="indent">กรณีผู้เช่าชำระล่วงหน้า 1 ปี ผู้ให้เช่ายินยอมลดให้ <span class="underline-sm">......</span> เดือน</p>
        <p class="indent">และกรณีผู้เช่าชำระล่วงหน้า <span class="underline-sm">......</span> เดือน ผู้ให้เช่ายินยอมลดให้ครึ่งเดือน</p>
    </div>

    <div class="article">
        <p><span class="article-number">ข้อ 3.</span> ผู้เช่าตกลงจะนำค่าเช่ามาชำระให้ ณ ที่ทำการของผู้ให้เช่าหรือตัวแทนภายกำหนด</p>
    </div>

    <div class="article">
        <p><span class="article-number">ข้อ 4.</span> ห้ามเช่าช่วงเป็นอนัต เว้นแต่ผู้ให้เช่าตกลงยินยอมด้วยเป็นลายลักษณ์อักษร</p>
    </div>

    <div class="article">
        <p><span class="article-number">ข้อ 5.</span> ค่าน้ำประปา ค่าไฟฟ้าและค่าใช้จ่ายอื่น หากมิให้เรียกเก็บตามอัตราในมิเตอร์หรือโดยเฉลี่ยจากผู้เช่า</p>
    </div>

    <div class="article">
        <p><span class="article-number">ข้อ 6.</span> ผู้เช่าต้องบำรุงรักษาห้องเช่ารวมถึงอุปกรณ์ไฟฟ้า มุ้งลวด กระจกบานเกล็ด กุญแจห้องกลอนประตู พัดลม หลอดไฟ เสื่อ ฝาผนัง และอื่นๆ ให้อยู่ในสภาพที่ดีอยู่เสมอ หากเกิดชำรุดเสียหายไม่ว่าด้วยเหตุใดก็ตาม ผู้เช่าต้องทำให้กลับคืนสู่สภาพเดิมทั้งทีด้วยค่าใช้จ่ายของผู้เช่า</p>
    </div>

    <div class="article">
        <p><span class="article-number">ข้อ 7.</span> ผู้เช่ามีหน้าที่รักษาความสะอาดตามกฎหมาย ไม่เก็บวัตถุไว้ไฟหรือสิ่งอันตรายหรือสิ่งต้องห้ามตามกฎหมาย ผู้เช่ายินยอมให้ผู้ให้เช่าเข้าตรวจความถูกต้องเรียบร้อยในห้อง</p>
    </div>

    <div class="article">
        <p><span class="article-number">ข้อ 8.</span> ผู้เช่าจะดัดแปลงต่อเติมหรือรื้อถอนทรัพย์สินที่เช่าทั้งหมดหรือบางส่วนได้ต่อเมื่อได้รับความยินยอมเป็นหนังสือจากผู้ให้เช่า</p>
    </div>

    <div class="article">
        <p><span class="article-number">ข้อ 9.</span> ถ้าผู้เช่าผิดสัญญาไม่ชำระค่าเช่าตามกำหนดไว้ในข้อ 2 ผู้ให้เช่าต้องทวงสิทธิไว้ในการกลับเข้าครอบครองทรัพย์สินที่เช่าตามสัญญานี้โดยฉบับพลัน และผู้เช่ายอมให้ผู้ให้เช่ายายบุคคลหรือทรัพย์สินของผู้เช่าออกไปจากทรัพย์ที่เช่าตามสัญญานี้</p>
    </div>

    <div class="article">
        <p><span class="article-number">ข้อ 10.</span> ผู้เช่ามีหน้าที่แจ้งให้ผู้ให้เช่าทราบล่วงหน้าในการเลิกเช่าไม่น้อยกว่าสามสิบวันจึงจะได้รับเงินล่วงหน้าคืนหรืออยู่อาศัยโดยหักจากการชำระล่วงหน้าตามข้อ 2</p>
    </div>

    <div class="article">
        <p><span class="article-number">ข้อ 11.</span> ในวันทำสัญญานี้ผู้เช่าได้ตรวจดูทรัพย์สินที่เช่าแล้วเห็นว่ามีสภาพปกติดีทุกประการและผู้ให้เช่าได้ส่งมอบทรัพย์สินที่เช่าให้แก่ผู้เช่าแล้ว</p>
    </div>

    <div class="article">
        <p><span class="article-number">ข้อ 12.</span> ผู้เช่าได้มอบสำเนาบัตรประจำตัว สำเนาทะเบียนบ้านและเอกสารแสดงตัวตามกฎหมายที่ไม่หมดอายุ พร้อมรูปถ่าย <span class="underline-sm">{{ $contract->photo_count ?? '......' }}</span> รูป ให้ไว้กับผู้ให้เช่า</p>
    </div>

    <div class="mt-4">
        <p>คู่สัญญาทั้งสองฝ่ายได้อ่านและทำความเข้าใจดีแล้ว จึงลงลายมือชื่อไว้เป็นหลักฐาน</p>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <p class="signature-line">ลงชื่อ ผู้เช่า</p>
            <p>({{ $guest->first_name ?? '' }} {{ $guest->last_name ?? '' }})</p>
        </div>
        <div class="signature-box">
            <p class="signature-line">ลงชื่อ ผู้ให้เช่า</p>
            <p>({{ $contract->landlord_name ?? '................................' }})</p>
        </div>
    </div>
    <div class="signature-section">
        <div class="signature-box">
            <p class="signature-line">ลงชื่อ พยาน</p>
        </div>
        <div class="signature-box">
            <p class="signature-line">ลงชื่อ พยาน</p>
        </div>
    </div>

    <div class="mt-4 text-center">
        <p><span class="bold">หมายเหตุ</span></p>
        <p>สัญญาเช่านี้มีอายุ {{ $contract->duration ?? '........................' }}</p>
        <p>เริ่มตั้งแต่วันที่ {{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') : '......................' }} ถึงวันที่ {{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') : '......................' }}</p>
    </div>
</body>
</html>
