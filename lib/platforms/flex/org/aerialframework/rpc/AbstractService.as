package org.aerialframework.rpc
{

	import flash.utils.getQualifiedClassName;

	import mx.rpc.AsyncToken;
	import mx.rpc.remoting.RemoteObject;

	import org.aerialframework.rpc.messages.AerialErrorMessage;
	import org.aerialframework.rpc.operation.Operation;
	import org.aerialframework.system.DoctrineQuery;

	public class AbstractService extends RemoteObject implements IService
	{
		AerialErrorMessage;

		private var _voClass:Class;

		private static var _serverURL:String = "http://localhost/aerial/index.php";

		public function AbstractService(source:String, voClass:Class)
		{
			super("Aerial");
			this.source = source;
			this.endpoint = SERVER_URL;
			_voClass = voClass;

			this.convertParametersHandler = preprocessArguments;
		}

		public function get voClass():Class
		{
			return _voClass;
		}

		public static function get SERVER_URL():String
		{
			return _serverURL;
		}

		public static function set SERVER_URL(value:String):void
		{
			_serverURL = value;
		}

		/*Modify Methods*/
		public function insert(vo:Object, returnCompleteObject:Boolean = false, mapToModel:Boolean=true):Operation
		{
			validateVO(vo);
			var op:Operation = new Operation(this, "insert", vo, returnCompleteObject, mapToModel);

			return op;
		}

		public function update(vo:Object, returnCompleteObject:Boolean = false, mapToModel:Boolean=true):Operation
		{
			validateVO(vo);
			var op:Operation = new Operation(this, "update", vo, returnCompleteObject, mapToModel);

			return op;
		}

		public function save(vo:Object, returnCompleteObject:Boolean = false, mapToModel:Boolean=true):Operation
		{
			validateVO(vo);
			var op:Operation = new Operation(this, "save", vo, returnCompleteObject, mapToModel);

			return op;
		}

		public function drop(vo:Object, mapToModel:Boolean=true):Operation
		{
			validateVO(vo);
			var op:Operation = new Operation(this, "drop", vo, mapToModel);

			return op;
		}

		/**
		 * Pre-processes an array of given arguments so that it will not send an array of arguments
		 * but rather a collection of arguments
		 *
		 * @param args The arguments to be sent to PHP
		 * @return
		 */
		public function preprocessArguments(args:Array):Array
		{
			return args[0];
		}

		// Find Methods

		public function find(criteria:* = null):Operation
		{
			var op:Operation = new Operation(this, "find", criteria);
			return op;
		}

		public function count():Operation
		{
			var op:Operation = new Operation(this, "count");

			return op;
		}

		public function executeDQL(query:DoctrineQuery):AsyncToken
		{
			var op:Operation = new Operation(this, "executeDQL", query.properties);
			return op.execute();
		}

		// Helpers

		private function validateVO(vo:Object):void
		{
			if(!(vo is _voClass))
				throw new ArgumentError(this.source + ".insert(vo:Object) argument must be of type " + getQualifiedClassName(_voClass) + " (You used " + getQualifiedClassName(vo) + ")");
		}
	}
}