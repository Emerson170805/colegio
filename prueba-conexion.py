import mysql.connector
from mysql.connector import Error

def probar_conexion():
    try:
        conexion = mysql.connector.connect(
            host="193.203.175.174",  # IP directa del servidor MySQL
            user="u505767678_estrada",
            password="shadow.2005.SHADOW",
            database="u505767678_colegio",
            auth_plugin='mysql_native_password'
        )

        if conexion.is_connected():
            print("✅ Conexión exitosa a la base de datos MySQL.")
            conexion.close()
        else:
            print("❌ No se pudo conectar.")

    except Error as e:
        print(f"❌ Error de conexión: {e}")

if __name__ == "__main__":
    probar_conexion()
