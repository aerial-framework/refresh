package org.aerialframework.rpc.operation
{
	import mx.rpc.AsyncToken;

	public interface IOperation
	{
		function callback(_resultHandler:Function, _faultHandler:Function = null, _tokenData:Object = null):Operation;
		function execute(offset:uint=0, limit:uint=0):AsyncToken;
	}
}