package org.aerialframework.rpc
{
	import org.aerialframework.rpc.operation.Operation;

	public interface IService
	{

		function find(arg:* = null):Operation;

		function save(vo:Object, returnCompleteObject:Boolean = false, mapToModel:Boolean = true):Operation;

		function insert(vo:Object, returnCompleteObject:Boolean = false, mapToModel:Boolean = true):Operation;

		function update(vo:Object, returnCompleteObject:Boolean = false, mapToModel:Boolean = true):Operation;

		function drop(vo:Object, mapToModel:Boolean = true):Operation;

		function count():Operation;
	}
}