import os
import sys
import json

if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')

print("==================================================================")
print("           MASTER SYSTEM VERIFICATION & INTEGRITY HARNESS         ")
print("==================================================================")

errors = []

def extract_quote_aware_json(text, decl, open_char='[', close_char=']'):
    idx = text.find(decl)
    if idx == -1:
        return None
    start = idx + len(decl)
    in_str = False
    esc = False
    depth = 0
    end = -1
    for i in range(start, len(text)):
        c = text[i]
        if in_str:
            if esc:
                esc = False
            elif c == '\\':
                esc = True
            elif c == '"':
                in_str = False
        else:
            if c == '"':
                in_str = True
            elif c == open_char:
                depth += 1
            elif c == close_char:
                depth -= 1
                if depth == 0:
                    end = i + 1
                    break
    if end != -1:
        return text[start:end]
    return None

# 1. Verify index.html & public_html/index.html
for p in ['index.html', 'public_html/index.html']:
    if not os.path.exists(p):
        errors.append(f"Missing file: {p}")
        continue
    sz = os.path.getsize(p)
    print(f"[OK] {p} exists ({sz:,} bytes)")
    with open(p, 'r', encoding='utf-8') as f:
        html = f.read()

    sub = extract_quote_aware_json(html, 'const pastQuestionsBank = ', '[', ']')
    if sub is None:
        errors.append(f"{p} missing pastQuestionsBank")
    else:
        try:
            past_data = json.loads(sub)
            print(f"     -> {p} pastQuestionsBank has {len(past_data)} questions.")
            if len(past_data) != 940:
                errors.append(f"{p} expected 940 past questions, got {len(past_data)}")
        except Exception as e:
            errors.append(f"{p} pastQuestionsBank JSON parse error: {e}")

    # Check study material screen & nav button
    if 'id="view-studymaterial"' not in html:
        errors.append(f"{p} missing view-studymaterial section")
    if 'id="nav-btn-studymaterial"' not in html:
        errors.append(f"{p} missing nav-btn-studymaterial button")

# 2. Verify testas.html & public_html/testas.html
for p in ['testas.html', 'public_html/testas.html']:
    if not os.path.exists(p):
        errors.append(f"Missing file: {p}")
        continue
    sz = os.path.getsize(p)
    print(f"[OK] {p} exists ({sz:,} bytes)")
    with open(p, 'r', encoding='utf-8') as f:
        html = f.read()

    sub = extract_quote_aware_json(html, 'const testasBundle = ', '{', '}')
    if sub is None:
        errors.append(f"{p} missing testasBundle")
    else:
        try:
            bundle_data = json.loads(sub)
            tq = len(bundle_data.get('questions', []))
            print(f"     -> {p} testasBundle has {tq} questions.")
            if tq != 418:
                errors.append(f"{p} expected 418 questions, got {tq}")
        except Exception as e:
            errors.append(f"{p} testasBundle JSON parse error: {e}")
            
    if 'function startExamSimulation(mode)' not in html:
        errors.append(f"{p} missing startExamSimulation(mode) function")

# 3. Verify past_papers.html & public_html/past_papers.html
for p in ['past_papers.html', 'public_html/past_papers.html']:
    if not os.path.exists(p):
        errors.append(f"Missing file: {p}")
        continue
    sz = os.path.getsize(p)
    print(f"[OK] {p} exists ({sz:,} bytes)")
    with open(p, 'r', encoding='utf-8') as f:
        html = f.read()
    sub = extract_quote_aware_json(html, 'const pastQuestionsBank = ', '[', ']')
    if sub is None:
        errors.append(f"{p} missing pastQuestionsBank")
    else:
        try:
            past_data = json.loads(sub)
            print(f"     -> {p} pastQuestionsBank has {len(past_data)} questions.")
            if len(past_data) != 940:
                errors.append(f"{p} expected 940 past questions, got {len(past_data)}")
        except Exception as e:
            errors.append(f"{p} pastQuestionsBank JSON parse error: {e}")

# 4. Verify study_material.html & public_html/study_material.html
for p in ['study_material.html', 'public_html/study_material.html']:
    if not os.path.exists(p):
        errors.append(f"Missing file: {p}")
        continue
    sz = os.path.getsize(p)
    print(f"[OK] {p} exists ({sz:,} bytes)")
    with open(p, 'r', encoding='utf-8') as f:
        html = f.read()
    if 'imatResourcesWithCoverage' not in html or 'testasResourcesWithCoverage' not in html:
        errors.append(f"{p} missing resource coverage definitions")

# 5. Verify study_material_data.js & public_html/study_material_data.js
for p in ['study_material_data.js', 'public_html/study_material_data.js']:
    if not os.path.exists(p):
        errors.append(f"Missing file: {p}")
        continue
    sz = os.path.getsize(p)
    print(f"[OK] {p} exists ({sz:,} bytes)")

# 6. Verify predictive_question_bank.js & public_html/predictive_question_bank.js
for p in ['predictive_question_bank.js', 'public_html/predictive_question_bank.js']:
    if not os.path.exists(p):
        errors.append(f"Missing file: {p}")
        continue
    sz = os.path.getsize(p)
    print(f"[OK] {p} exists ({sz:,} bytes)")
    with open(p, 'r', encoding='utf-8') as f:
        js = f.read()
    sub = extract_quote_aware_json(js, 'window.predictiveQuestionsBank = ', '[', ']')
    if sub is None:
        errors.append(f"{p} missing predictiveQuestionsBank")
    else:
        try:
            pred_data = json.loads(sub)
            print(f"     -> {p} predictiveQuestionsBank has {len(pred_data)} questions.")
            if len(pred_data) != 3000:
                errors.append(f"{p} expected 3000 questions, got {len(pred_data)}")
        except Exception as e:
            errors.append(f"{p} predictiveQuestionsBank JSON parse error: {e}")

# 7. Byte synchronization checks
pairs = [
    ('index.html', os.path.join('public_html', 'index.html')),
    ('testas.html', os.path.join('public_html', 'testas.html')),
    ('past_papers.html', os.path.join('public_html', 'past_papers.html')),
    ('study_material.html', os.path.join('public_html', 'study_material.html')),
    ('study_material_data.js', os.path.join('public_html', 'study_material_data.js')),
    ('predictive_question_bank.js', os.path.join('public_html', 'predictive_question_bank.js'))
]
for src, dst in pairs:
    if os.path.exists(src) and os.path.exists(dst):
        s_sz = os.path.getsize(src)
        d_sz = os.path.getsize(dst)
        if s_sz == d_sz:
            with open(src, 'rb') as f1, open(dst, 'rb') as f2:
                if f1.read() == f2.read():
                    print(f"[OK] Perfect synchronization: {src} == {dst}")
                else:
                    errors.append(f"Content mismatch between {src} and {dst}")
        else:
            errors.append(f"Size mismatch: {src} ({s_sz}b) vs {dst} ({d_sz}b)")

print("\n------------------------------------------------------------------")
if not errors:
    print(">>> 100% SUCCESS: ALL 12 MASTER ARTIFACTS VALIDATED, SYNCHRONIZED, & CERTIFIED! <<<")
    print("------------------------------------------------------------------")
    sys.exit(0)
else:
    print(f">>> FOUND {len(errors)} ERRORS:")
    for err in errors:
        print(f"  [X] {err}")
    print("------------------------------------------------------------------")
    sys.exit(1)
