#include "myvector.h"
bool Thing::verbose = false;
size_t Thing::last_alloc = 0;

/**
 * @brief MyVector::MyVector Construct a vector with size 0
 *
 * Remember that the data pointer should point to nothing, and
 * counter variables should be initialised.
 */
MyVector::MyVector()
{
	public:
	int n_space = 0;
	int n_items;
	int *data = nullptr;
	
	MyVector (size_t x, size_t y, size_t d){
		n_space = x;
		n_items = y;
		data = d;
}

/**
 * @brief MyVector::~MyVector Free any memory that you have allocated do this last
 */
MyVector::~MyVector(size_t *data )
{
    delete data;
}

/**
 * @brief MyVector::size
 * @return The number of items in the vector
 */
size_t MyVector::size(int n_items) const
{
	return n_items;
}

/**
 * @brief MyVector::allocated_length
 * @return The lenght of the allocated data buffer
 */
size_t MyVector::allocated_length(int n_space) const
{
	// returning the space or lenght allocated
	return n_space;
}

/**
 * @brief MyVector::push_back
 * @param t The thing to add
 *
 * Add a thing to the back of the vector.
 * Remember to check if there is enough space to insert it first.
 * If there is not enough space, then you should reallocate the buffer
 * and copy each thing accross. When expanding the buffer, double the
 * allocated size.
 */
void MyVector::push_back(const Thing &t, int n_items, int n_space, int *data)
{
	// adding an item to the back of an array
	// check if there is enough space
	// if there is space
	data = new int[n_space];
	if (n_items < n_space){
		data[n_items] = t;
		n_items++;
	} 
	// if there is no space
	else if( n_items == n_space){
		// double the size of the original array
		new_space = 2 * n_space;
		new_data = new int[new_space];
		for (int i = 0; i < new_space; i++){
			new_data[i] = data[i];
		}
		delete[] data;
	}
	else{
		return -1;
	}
}

/**
 * @brief MyVector::pop_back
 * Remove the last item from the back.
 * Reallocate with half the space if less than a quarter of the vector is used.
 */
void MyVector::pop_back(int *data, int n_space, int n_items)
{
	// index for the element at the back and remove it
	data = new int[n_space];
	data[n_space - 1] == NULL;
	// clear un-used space
	if (n_space == 4*n_items){
		new_space = n_space/4;
		new_data = new int[new_space];
		for (int i = 0; i < new_space; i++){
			new_data[i] = data[i];
		}
	}
}

/**
 * @brief MyVector::front
 * @return A reference to the first item in the array.
 * I will never call this on an empty list.
 */
Thing &MyVector::front(int *data, int n_space)
{
	if (n_space != 0){
		data = new int[n_space];
		return data[0];
	}
}

/**
 * @brief MyVector::back
 * @return A reference to the last item in the array.
 *
 * Note that this might not be the back of the data buffer.
 * I will never call this on an empty list.
 */
Thing &MyVector::back(int *data, int n_space)
{	
	data = new int[n_space];
	return &data[n_space - 1];
}

/**
 * @brief MyVector::begin
 * @return A pointer to the first thing.
 */
Thing *MyVector::begin(int *data, int n_space)
{
	data = new int[n_space];
	*dp = data[0];
	return *dp
}

/**
 * @brief MyVector::end
 * @return A pointer to the memory address following the last thing.
 */
Thing *MyVector::end(int *data, int n_space)
{
	data = new int[n_space];
	*dp = data[n_space - 1];
	return *dp;
}

/**
 * @brief MyVector::operator []
 * @param i
 * @return A reference to the ith item in the list.
 */
Thing &MyVector::operator[](size_t i, int *data, int n_space)
{
	data = new int[n_space];
	return &data[i];
}

/**
 * @brief MyVector::at
 * @param i
 * @return A reference to the ith item in the list after checking
 * that the index is not out of bounds.
 */
Thing &MyVector::at(size_t i, int *data, int n_space)
{
	data = new int[n_space];
	if (i >= 0 && i < n_space){
		return &data[i];
	}
}

/**
 * @brief MyVector::reallocate
 * @param new_size
 * Reallocate the memory buffer to be "new_size" length, using new Thing[new_size]
 * Copy all items from the old buffer into the new one.
 * Delete the old buffer using delete[]
 */
void MyVector::reallocate(size_t new_size, int *int data, int n_space, n_items)
{
	data = new int[n_space];
	if (n_space == n_items){
		new_size = 2*n_space;
		new_data = new int[new_size];
		for (int i = 0; i < new_size; i++){
			new_data[i] = data[i];
			delete[] data;
		}
	}
}

