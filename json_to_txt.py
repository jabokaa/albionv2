import json

LANGS = {
    "PT-BR": "Português",
    "EN-US": "Inglês",
    "ES-ES": "Espanhol",
    "FR-FR": "Francês",
}

with open("categorias.json", encoding="utf-8") as f:
    data = json.load(f)

lines = []
for entry in data:
    tuid = entry.get("@tuid", "???")
    lines.append(f"[ {tuid} ]")

    tuv = entry.get("tuv", [])
    if isinstance(tuv, dict):
        tuv = [tuv]
    traducoes = {t["@xml:lang"]: t["seg"] for t in tuv if isinstance(t, dict)}
    for code, nome in LANGS.items():
        valor = traducoes.get(code, "(não encontrado)")
        lines.append(f"  {nome}: {valor}")

    lines.append("")

output = "\n".join(lines)
with open("categorias.txt", "w", encoding="utf-8") as f:
    f.write(output)

print(f"{len(data)} categorias exportadas para categorias.txt")
