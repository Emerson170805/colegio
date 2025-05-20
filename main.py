import os
import threading
import time
from datetime import datetime
import cv2
import mysql.connector
from flask import Flask, Response, jsonify, render_template
from flask_cors import CORS
from mysql.connector import Error
from pyzbar.pyzbar import decode

app = Flask(__name__)
CORS(app)

# Estado global del último escaneo
ultimo_dato = {
    "nombre": "", "apellido": "", "grado": "", "seccion": "",
    "nivel": "", "fecha": "", "hora": "", "mensaje": ""
}

camara = None


def conectar_db():
    try:
        return mysql.connector.connect(
            host="193.203.175.174",
            user="u505767678_estrada",
            password="shadow.2005.SHADOW",
            database="u505767678_colegio"
        )
    except Error as e:
        print(f"❌ Error al conectar a MySQL: {e}")
        return None


def inicializar_camara(indice=0):
    cam = cv2.VideoCapture(indice)
    if not cam.isOpened():
        print(f"❌ No se pudo abrir la cámara (índice {indice}).")
        return None
    print(f"✅ Cámara {indice} abierta correctamente.")
    return cam


def procesar_qr_y_marcar_asistencia(frame):
    global ultimo_dato
    codigos = decode(frame)
    if not codigos:
        return

    for codigo in codigos:
        dni = codigo.data.decode("utf-8")
        conn = conectar_db()
        cursor = None
        try:
            if conn is None:
                raise Exception("No se pudo conectar a la base de datos.")
            cursor = conn.cursor(dictionary=True)
            cursor.execute("SELECT * FROM alumno WHERE dni = %s", (dni,))
            alumno = cursor.fetchone()

            fecha_hoy = datetime.now().date()
            if alumno:
                cursor.execute(
                    "SELECT * FROM registro WHERE id_alumno = %s AND fecha = %s",
                    (alumno["id"], fecha_hoy)
                )
                ya = cursor.fetchone()

                if ya and ya["asistencia"] != "ausente":
                    ultimo_dato = {
                        "nombre": alumno["nombre"],
                        "apellido": alumno["apellido"],
                        "grado": alumno["grado"],
                        "seccion": alumno["seccion"],
                        "nivel": alumno["nivel"],
                        "fecha": str(fecha_hoy),
                        "hora": str(ya["hora"])[:8] if ya["hora"] else "",
                        "mensaje": f"{alumno['nombre']} ya registró asistencia."
                    }
                else:
                    hora_actual = datetime.now().time()
                    if ya:
                        cursor.execute(
                            "UPDATE registro SET asistencia='asistio', hora=%s "
                            "WHERE id_alumno=%s AND fecha=%s",
                            (hora_actual, alumno["id"], fecha_hoy)
                        )
                    else:
                        cursor.execute(
                            "INSERT INTO registro (id_alumno, fecha, asistencia, hora) "
                            "VALUES (%s, %s, %s, %s)",
                            (alumno["id"], fecha_hoy, "asistio", hora_actual)
                        )
                    conn.commit()
                    ultimo_dato = {
                        "nombre": alumno["nombre"],
                        "apellido": alumno["apellido"],
                        "grado": alumno["grado"],
                        "seccion": alumno["seccion"],
                        "nivel": alumno["nivel"],
                        "fecha": str(fecha_hoy),
                        "hora": str(hora_actual)[:8],
                        "mensaje": "Asistencia registrada correctamente."
                    }
            else:
                ultimo_dato = {
                    "nombre": "", "apellido": "", "grado": "", "seccion": "",
                    "nivel": "", "fecha": "", "hora": "",
                    "mensaje": f"DNI {dni} no encontrado en alumnos."
                }

        except Exception as e:
            print(f"❌ Error en el procesamiento de QR: {e}")
            ultimo_dato = {
                "nombre": "", "apellido": "", "grado": "", "seccion": "",
                "nivel": "", "fecha": "", "hora": "",
                "mensaje": f"Error al conectar a la base de datos: {e}"
            }
        finally:
            if cursor: cursor.close()
            if conn: conn.close()


def loop_escaneo():
    global camara
    while True:
        if camara and camara.isOpened():
            ok, frame = camara.read()
            if ok:
                procesar_qr_y_marcar_asistencia(frame)
            else:
                print("⚠️ No se pudo capturar frame.")
        else:
            print("⚠️ Cámara no disponible.")
        time.sleep(0.1)


@app.route("/")
def index():
    return render_template("index.html")


@app.route("/datos")
def datos():
    return jsonify(ultimo_dato)


@app.route("/video_feed")
def video_feed():
    def generar_video():
        while True:
            if camara and camara.isOpened():
                ok, frame = camara.read()
                if not ok:
                    continue
                _, buf = cv2.imencode(".jpg", frame)
                yield (
                    b"--frame\r\n"
                    b"Content-Type: image/jpeg\r\n\r\n" +
                    buf.tobytes() +
                    b"\r\n"
                )
            else:
                break

    if not camara or not camara.isOpened():
        return "Cámara no disponible.", 503
    return Response(
        generar_video(),
        mimetype="multipart/x-mixed-replace; boundary=frame"
    )


if __name__ == "__main__":
    # Solo inicializa cámara y thread **en el proceso de Flask que usa**
    # para servir (evita doble inicialización del reloader).
    if os.environ.get("WERKZEUG_RUN_MAIN") == "true":
        camara = inicializar_camara(0)
        if not camara:
            raise RuntimeError("❌ No se encontró ninguna cámara.")
        hilo = threading.Thread(target=loop_escaneo, daemon=True)
        hilo.start()
        print("🚀 Cámara y escaneo QR activos.")

    # Arranca Flask en puerto 5001
    app.run(
        host="0.0.0.0",
        port=5001,
        debug=True,
        threaded=True
    )
