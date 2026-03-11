from pathlib import Path
path = Path('resources/views/invoices/create.blade.php')
text = path.read_text()
lines = text.splitlines()
start = 51
end = 67
new_block = [
'                        <div class="row mb-4">',
'                            <div class="col-md-4">',
'                                <label class="form-label required">จำนวนเงิน (บาท)</label>',
'                                <input type="number" name="amount" class="form-control @error(\'amount\') is-invalid @enderror" required min="0" step="0.01" value="{{ old(\'amount\') }}">',
'                                @error(\'amount\')',
'                                    <div class="invalid-feedback">{{ $message }}</div>',
'                                @enderror',
'                            </div>',
'                            <div class="col-md-4">',
'                                <label class="form-label required">ภาษี (บาท)</label>',
'                                <input type="number" name="tax" class="form-control @error(\'tax\') is-invalid @enderror" required min="0" step="0.01" value="{{ old(\'tax\') }}">',
'                                @error(\'tax\')',
'                                    <div class="invalid-feedback">{{ $message }}</div>',
'                                @enderror',
'                            </div>',
'                            <div class="col-md-4">',
'                                <label class="form-label required">�ӹǹ�Թ��� (�ҷ)</label>',
'                                <input type="number" name="total" class="form-control @error(\'total\') is-invalid @enderror" min="0" step="0.01" value="{{ old(\'total\') }}" readonly>',
'                                @error(\'total\')',
'                                    <div class="invalid-feedback">{{ $message }}</div>',
'                                @enderror',
'                            </div>',
'                        </div>',
''
]
lines[start:end] = new_block
path.write_text('\r\n'.join(lines) + '\r\n')
