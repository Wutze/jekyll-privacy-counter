#!/bin/bash
set -e

SRC="361-grad-lokal/"
DEST="361-grad-webseite/"
GIT_REPO="http://gitlab1.home/micro/361neu"

# --- Testmodus starten ---------------------------------------------------
echo "🚀 Starte lokale Testumgebung (mit Drafts und Livereload)..."
cd "$SRC"

#bundle exec jekyll serve --unpublished --drafts --livereload
JEKYLL_ENV=development bundle exec jekyll serve --future --unpublished --drafts --livereload
SERVE_EXIT_CODE=$?

cd ..

# --- Nach dem Beenden fragen ---------------------------------------------
echo ""
echo "🛑 Jekyll wurde beendet (Exit-Code: $SERVE_EXIT_CODE)"
read -p "Möchtest du jetzt die Hauptwebseite aktualisieren? (j/N): " answer

if [[ "$answer" =~ ^[Jj]$ ]]; then
  echo ""
  echo "🔍 Synchronisiere Änderungen von $SRC → $DEST ..."

  # Änderungen synchronisieren
  RSYNC_OUTPUT=$(rsync -av --delete \
    --exclude '_site/' \
    --exclude '_drafts/' \
    --exclude '.jekyll-cache/' \
    --exclude '.sass-cache/' \
    --exclude '.git/' \
    --exclude 'Gemfile.lock' \
    --exclude '.gitignore' \
    "$SRC" "$DEST")

  echo "✅ Synchronisierung abgeschlossen."
  echo "$RSYNC_OUTPUT" | grep -E '^>f|^deleting' || echo "ℹ️  Keine Dateien verändert."

  cd "$DEST"

  # --- Produktions-Build --------------------------------------------------
  echo ""
  echo "🏗️  Erstelle Produktions-Build ..."
  JEKYLL_ENV=production bundle exec jekyll build
  echo "🎉 Hauptwebseite aktualisiert!"
  cd ..
  cd "$SRC"
  # --- GitLab-Backup ------------------------------------------------------
  echo ""
  echo "💾 Prüfe auf Änderungen für GitLab-Backup ..."

  if [ ! -d ".git" ]; then
    echo "🧩 Initialisiere Git-Repository..."
    git init
    git remote add origin "$GIT_REPO"
    git branch -M main
  fi

  # Prüfen, ob Änderungen existieren
  if [[ -n $(git status --porcelain) ]]; then
    echo "🟢 Änderungen erkannt – Backup wird erstellt."
    git add .
    COMMIT_MSG="Automatisches Backup: $(date '+%Y-%m-%d %H:%M:%S')"
    git commit -m "$COMMIT_MSG"
    git push -u origin main
    echo "✅ Backup erfolgreich auf GitLab gespeichert."
  else
    echo "🟡 Keine Änderungen erkannt – Backup übersprungen."
  fi

else
  echo "🕊️  Kein Upload – Änderungen bleiben lokal."
fi

