#include "myvector.h"
bool Thing::verbose = false;
size_t Thing::last_alloc = 0;


MyVector::MyVector(){
	n_items = 0;
	n_allocated = 0;
	data = nullptr;
}
MyVector::~MyVector(){
	
    delete[] data;
}
size_t MyVector::size() const{
	return n_items;

}
size_t MyVector::allocated_length() const{
	return n_allocated;
}

void MyVector::push_back(const Thing &t){
	if(n_allocated == 0){
		reallocate (1);
	}
	else if (n_allocated == n_items){
		reallocate (n_allocated*2);
	}
	data[n_items] = t;
	n_items ++;

}

/**
 * @brief MyVector::pop_back
 * Remove the last item from the back.
 * Reallocate with half the space if less than a quarter of the vector is used.
 */
void MyVector::pop_back(){
	n_items --;
	if(n_items<n_allocated/4){
		reallocate(n_allocated/2);
	}
}

/**
 * @brief MyVector::front
 * @return A reference to the first item in the array.
 * I will never call this on an empty list.
 */
Thing &MyVector::front(){
	return data[0];

}

/**
 * @brief MyVector::back
 * @return A reference to the last item in the array.
 *
 * Note that this might not be the back of the data buffer.
 * I will never call this on an empty list.
 */
Thing &MyVector::back(){
        return data[n_items - 1];

}

/**
 * @brief MyVector::begin
 * @return A pointer to the first thing.
 */
Thing *MyVector::begin(){
	return data;

}

/**
 * @brief MyVector::end
 * @return A pointer to the memory address following the last thing.
 */
Thing *MyVector::end(){
	return data + n_items;

}

/**
 * @brief MyVector::operator []
 * @param i
 * @return A reference to the ith item in the list.
 */
Thing &MyVector::operator[](size_t i){
        return data[i];
}

/**
 * @brief MyVector::at
 * @param i
 * @return A reference to the ith item in the list after checking
 * that the index is not out of bounds.
 */
Thing &MyVector::at(size_t i){
	if(i < 0 || i>= n_items){
		throw"junkie";
	}
	return data[i];
}

/**
 * @brief MyVector::reallocate
 * @param new_size
 * Reallocate the memory buffer to be "new_size" length, using new Thing[new_size]
 * Copy all items from the old buffer into the new one.
 * Delete the old buffer using delete[]
 */
void MyVector::reallocate(size_t new_size){
	n_allocated = new_size;
        Thing*temp = new Thing [new_size];
	
        for(size_t i = 0; i<n_items; i++){
		temp[i] = data[i];
	}
	delete[] data;
	data = temp;

}

