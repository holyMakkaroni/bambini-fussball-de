import type { Schemas } from '#shopware'

export const useProductReviewHelper = (productReviews: Schemas['ProductReview'][]|null) => {
  const maxPoints = 5
  const productReviewMatrix = ref<any>([])

  const groupByPoints = (values: Schemas['ProductReview'][]|null) => {
    if (!values) {
      return []
    }
    return values.reduce((rv, x) => {
      // @ts-ignore
      (rv[x.points] = rv[x.points] || []).push(x)
      return rv
    }, [])
  }

  const getProductReviewMatrix = (reviews: Schemas['ProductReview'][]|null) => {
    if (!reviews) {
      return
    }
    const matrix = []
    const groupedReviews: any[] = groupByPoints(reviews)

    for (let i = 1; i <= maxPoints; i++) {
      matrix[i] = {
        points: i,
        percent: 0,
        elements: []
      }

      if (groupedReviews && groupedReviews[i]) {
        matrix[i].elements = groupedReviews[i]

        if (groupedReviews[i].length) {
          matrix[i].percent = (groupedReviews[i].length / reviews.length) * 100
        }
      }
    }
    matrix.shift()

    return matrix.reverse()
  }

  if (productReviews) {
    productReviewMatrix.value = getProductReviewMatrix(productReviews)
  }

  return {
    groupByPoints,
    maxPoints,
    productReviewMatrix
  }
}
